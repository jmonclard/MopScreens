# -*- coding: utf8 -*-
"""
Un petit envoi de punch
python3 sendpunch.py -R /dev/serial/by-id/usb-Prolific_Technology_Inc._USB-Serial_Controller-if00-port0 -v -v -v -v -v -f /var/www/cfco/pictures/serverip.txt -C /dev/serial/by-id/usb-FTDI_Dual_RS232-HS-if01-port0 -D /var/www/cfco/pictures/radiolog.txt

"""


# following PEP 386: N.N[.N]+[{a|b|c|rc}N[.N]+][.postN][.devN]
VERSION=(1,0,1)
PREREL=('a',1)
POST=0
DEV=0

"""
Historique des versions (a.b.c)
--1.0.0 version initiale
--1.0.1 ajout du get pour le radioupdate
"""


HOST = '192.168.0.10'
GET = '/cfco/screenradioupdate.php'
PORT = 80

def get_version():
    version='.'.join(map(str,VERSION))
    if PREREL:
        version+=PREREL[0]+'.'.join(map(str,PREREL[1:]))
    if POST:
        version+=".post"+str(POST)
    if DEV:
        version+=".dev"+str(DEV)
    return version

__version__ = get_version()
__authors__ = 'Metraware'
__contact__ = 'info@metraware.com'

import struct
import argparse
import sys
import traceback
import socket
import doctest
import coloredlogs, logging
import termcolor
import os
import binascii
import signal
import sys
import datetime
import threading
import serial
import time
import socket
import random
import shutil
import queue
import signal

def signal_handler(signal, frame):
    print(termcolor.colored("Ctrl+C !!!",'red'))
    sys.exit(4)

"""
#pour la gestion des pertes de periph usb
class ExitCommand(Exception):
  pass

def signal_handler(signal, frame):
  raise ExitCommand()

signal.signal(signal.SIGUSR1, signal_handler)
"""

class logger(object):
    """
    class logger, un logger qui logge
    """
    def __init__(self, level):
        self.log = logging.getLogger("sendpunchLogger")
        level = logging.WARNING - 10 * level # warning correspond ici au niveau minimum (on voit donc toujours les warning, error et critical)
        if level < logging.NOTSET:
            level = logging.NOTSET

        #coloredlogs.install(fmt='%(asctime)s.%(msecs)03d %(levelname)-8s %(funcName)-20s %(filename)-20s %(lineno)-5d %(message)s', datefmt='%H:%M:%S', level=level)
        coloredlogs.install(fmt='%(asctime)s.%(msecs)03d %(levelname)-8s %(funcName)-20s %(filename)15s %(lineno)5d %(message)s', datefmt='%H:%M:%S', level=level)
        #coloredlogs.install(datefmt='%H:%M:%S', level=level)
        self.log.debug("Log started")

        # "Console"
        """
        Pas nécessaire quand il y a le coloredlogs
        #fmt = logging.Formatter(fmt='%(asctime)s.%(msecs)03d %(levelname)-8s %(funcName)-20s %(lineno)d %(message)s', datefmt='%H:%M:%S')
        #self.log.setLevel(level)
        con = logging.StreamHandler()
        con.setLevel(level)  # logging.DEBUG
        con.setFormatter(fmt)
        self.log.addHandler(con)
        """

    def getlogger(self):
        return self.log

    """
    0253101910031002101FCF1000A6101E1007812E03
    02 53 10 19 10 03 10 02 10 1F CF 10 00 A6 10 1E 10 07 81 2E 03
    02 53 19 03 02 1F CF 00 A6 1E 07 81 2E 03

    0253101910031002101FCF1000A620100C1005101403
    02 53 19 03 02 1F CF 00 A6 20 0C 05 14 03

    0253101910031002101FCF1000A6221011895903
    0253101910031002101FCF1000A62510019B3903
    0253101910031002101FCF1000A627100710172E03
    0253101910031002101FCF1000A6291007B32D03
    0253101910031002101FCF1000A62B100B3F100603
    0253101910031002101FCF1000A62D100CAB101703
    0253101910031002101FCF1000A62F100BA7100503
    0253101910031002101FCF1000A6321002E93303
    0253101910031002101FCF1000A6341010FD5F03
    0253101910031002101FCF1000A6371002F73303

    """

def getnextbyte(data, index):
    """
    >>> a,b = getnextbyte(binascii.unhexlify('1210233445'), 1)
    >>> print(hex(a))
    0x23
    >>> print(b)
    3
    >>> a,b = getnextbyte(binascii.unhexlify('1210233445'), 0)
    >>> print(hex(a))
    0x12
    >>> print(b)
    1

    :param data:
    :param index:
    :return:
    """
    v = data[index]
    index += 1
    if v == 0x10:
        v = data[index]
        index += 1

    return v,index

class MyApplication(object):
    def __init__(self, argv):
        # gestion des arguments de la ligne de commande de cette merveilleuse application
        parser = argparse.ArgumentParser(description="sendpunch",
                                         epilog="python sendpunch.py -t12345 -c65 -s1001950 -v -v -v -v -i192.168.0.56 -CCOM1: -v\r\npython3 sendpunch.py -v -v -v -v -f/var/www/cfco/pictures/serverip.txt  -C/dev/ttyUSB1\r\npython3 /home/lpacaco/LoRa/sendpunch.py -f/var/www/cfco/pictures/serverip.txt -C/dev/serial/by-id/usb-FTDI_Dual_RS232-HS-if01-port0 -D/var/www/cfco/pictures/radiolog.txt")
        parser.add_argument('-p',
                            '--port',
                            dest='port',
                            default=10000,
                            help="destination port")
        parser.add_argument('-i',
                            '--ip',
                            dest='ip',
                            default='localhost',
                            help="destination ip address")
        parser.add_argument('-f',
                            '--filewithip',
                            dest='ipfile',
                            default='/var/www/cfco/pictures/serverip.txt',
                            help="text file with destination ip address (a simple text line with ip address, ex:192.168.0.50)")
        parser.add_argument('--commandfile',
                            dest='commandfile',
                            default='/var/www/cfco/pictures/command.txt',
                            help="text file with destination ip address (a simple text line with ip address, ex:192.168.0.50)")
        parser.add_argument('-g',
                            dest='simulateget',
                            action="store_true",
                            default=None,
                            help="simulate a GET access to " + HOST + GET + " page, take care, this create some records")
        parser.add_argument('-G',
                            dest='simulatemultipleget',
                            action="store_true",
                            default=None,
                            help="simulate multiple GET access to " + HOST + GET + " page, take care, this create some records")
        parser.add_argument('-D',
                            '--debugfile',
                            dest='debugfile',
                            default=None,
                            help="text file where debug/alive information are stored")
        parser.add_argument('-v',
                            dest="verbose",
                            action="count",
                            default=0,
                            help="increase verbosity level (can be used many times)")
        parser.add_argument('-T',
                            dest="showtest",
                            action="store_true",
                            default=False,
                            help="display unit test results, even if everything works fine")
        parser.add_argument('-t',
                            '--time',
                            dest='codetime',
                            default=None,
                            help="code time, time tenths of seconds after 00:00:00")
        parser.add_argument('-c',
                            '--codenumber',
                            dest='codenumber',
                            default=None,
                            help="code number, 2 byte 0-65K or the code of a SpecialPunch")
        parser.add_argument('-s',
                            '--sicardno',
                            dest='sicardno',
                            default=None,
                            help="SI card no, 4 byte integer  -2GB until +2GB")
        parser.add_argument('-d',
                            '--decode',
                            dest='decode',
                            default=None,
                            help="May contain an hexa string (like 0253101910031002101FCF1000A6221011895903), \
                            if this string is valid, value decoded are used instead of others parameters")
        parser.add_argument('-C',
                            '--COMPORT',
                            dest='comport',
                            default=None,
                            help="if exists, application waits for a serial port for some data to decode (expect punch communication)")

        parser.add_argument('-R',
                            '--RECPORT',
                            dest='recport',
                            default=None,
                            help="if exists, application waits for a serial port for some data to decode (expect radio communication)")

        parser.add_argument('-S',
                            '--SRRPORT',
                            dest='srrport',
                            default="/dev/serial/by-id/usb-Silicon_Labs_SPORTident_USB_to_UART_Bridge_Controller_2652-if00-port0",
                            help="if exists, application waits for a serial port for some data to decode (expect srr communication)")

        self.args = parser.parse_args(argv)
        self.log = logger(self.args.verbose).getlogger()

        self.log.debug("Arg done")
        self.previoustime = None
        self.commandprevioustime = None
        self.sent = 0
        self.thcomport = None
        self.threcport = None
        self.thsrrport = None
        self.localRSSI = None
        self.localPosteID = None
        self.localCardNR = None
        self.localBatPoste = None
        self.localBatLevel = None
        self.localSigPoste = None
        self.localSigLevel = None
        self.localStatus = None
        self.commandToDo = ["INSTSTART"]
        self.installMode = False
        self.punchMemory = []

        self.receptionQueue = queue.Queue()

        try:
          os.remove(self.args.commandfile)
        except Exception as e:
          pass

    def docommand(self):
        try:
            if self.args.commandfile is not None:
                if os.path.isfile(self.args.commandfile):
                  if self.commandprevioustime != self.modification_date(self.args.commandfile):
                      self.log.info("Command file changed !")
                      self.commandprevioustime = self.modification_date(self.args.commandfile)
                      with open(self.args.commandfile, "r") as f:
                          for line in f:
                              line = line.strip()

                              if line and not line.startswith("#"):
                                self.commandToDo.append(line)
                                self.log.debug("Commands are " + str(self.commandToDo))
                                self.dumpInfo("C\tCommand is " + str(line))
        except Exception as e:
            self.log.error(str(e))
            self.dumpInfo("E\t" + str(e))

    def getip(self):
        try:
            if self.args.ipfile is not None:
                if not os.path.isfile(self.args.ipfile):
                    raise Exception(self.args.ipfile + " not found")
                if self.previoustime != self.modification_date(self.args.ipfile):
                    self.log.info("IP file changed !")
                    self.previoustime = self.modification_date(self.args.ipfile)
                    with open(self.args.ipfile, "r") as f:
                        for line in f:
                            line = line.strip()

                            if line and not line.startswith("#"):
                                self.args.ip = line
                                self.log.debug("Using " + line + " from " + self.args.ipfile)
                                break
                    self.log.info("IP value is " + str(self.args.ip))
                    self.dumpInfo("L\tIP is " + str(self.args.ip))
        except Exception as e:
            self.log.error(str(e))
            self.dumpInfo("E\t" + str(e))

    def modification_date(self, filename):
        t = os.path.getmtime(filename)
        return datetime.datetime.fromtimestamp(t)

    def dumpInfo(self, info):
        self.log.info(info)
        if self.args.debugfile is not None:
            with open(self.args.debugfile, "a") as myfile:
                myfile.write(str(datetime.datetime.now()) + "\t" + info + "\r\n")
        else:
            self.log.info("No debug file")

    def workWithReceivedData(self, r_str, info):
        self.localPosteID = ""
        self.localCardNR = ""
        self.localBatPoste = ""
        self.localBatLevel = ""

        tag = ""

        decal = 0
        decalinc = 0
        lenstr = len(r_str)
        done = lenstr  == 0
        error = False

        self.dumpInfo('I' + "\t" + str(info).strip())
        while not done and not error:
            # test du premier octet (2 chars)
            tag = r_str[decal:decal+2]
            if tag == 'FF':
                # des fois des caractères étranges apparaissent !
                decalinc = 2
            elif tag == '02':
                # potentiellement un punch
                decalinc = 38
                datatosend, nextindex = self.decode_punch(r_str[decal:decal+38])
                if datatosend:
                    self.dumpInfo('P' + "\t" + str(self.localPosteID) + "\t" + str(self.localCardNR))
                    self.sendto(datatosend)
            elif tag == '3A':
                # potentiellement des infos batteries
                decalinc = 10
                self.decodeBatteryInfo(r_str[decal+2:decal+10])
            elif tag == '3B':
                # potentiellement des infos niveau de signal
                decalinc = 8
                self.decodeSignalInfo(r_str[decal+2:decal+8])
            elif tag == 'DE':
                # potentiellement un DEADBEEF
                tagcomplet = r_str[decal:decal+8]
                if tagcomplet == 'DEADBEEF':
                  decalinc = 8
                else:
                  raise Exception("Cannot decode after DE " + str(r_str))
            elif tag == '54':
                # potentiellement un TOPNOW qui revient depuis un relais
                tagcomplet = r_str[decal:decal+12]
                if tagcomplet == '544F504E4F57':
                  decalinc = 12
                else:
                  raise Exception("Cannot decode after 54 " + str(r_str))
            elif tag == '49':
                # potentiellement un INSTDONE** qui revient depuis un relais
                tagcomplet = r_str[decal:decal+20]
                if tagcomplet == '494E5354444F4E452A2A':
                  decalinc = 20
                else:
                  raise Exception("Cannot decode after 49 " + str(r_str))
            else:
                raise Exception("Cannot decode " + str(r_str))
            decal += decalinc
            done = done or decal >= lenstr

        if not error:
            self.updateInfo(self.localBatPoste, 0, self.localBatLevel, self.localRSSI, self.localStatus)

    def waitReply(self, ser):
      test = 3
      done = False
      r_str = ""
      try:
        while test > 0 and not done:
          r = ser.readline() # ok ou erreur
          r_str = r.decode('utf-8').strip()
          #self.log.debug("reception de " + r_str)
          if r_str.startswith("+RFRX"):
            self.receptionQueue.put(r_str)
          done = r_str.startswith("OK") or r_str.startswith("ERROR")
          test -= 1
      except serial.SerialException as e:
        pass
      return r_str

    def loraTransact(self, ser, cmd):
      self.log.debug("Sending:" + cmd)
      ser.write((cmd + "\n").encode())
      self.log.debug("Waiting reply")
      r_str = self.waitReply(ser)
      self.log.debug(" => " + r_str)

    def rxEnabler(self, ser, enable):
      if enable:
        self.loraTransact(ser, "AT+RFRX=CONTRX")# OK Start a Continuous RX
      else:
        self.loraTransact(ser, "AT+RFRX=STOP")

    """ pas utile
    def watchdogThread(self, params):
      self.log.debug("Dans le WATCHDOG THREAD " + str(params))
      localser = params
      self.log.debug("Surveillance du port " + localser.port)

      while True:
        if not os.path.exists(localser.port):
          self.dumpInfo("E\tPerte du port " + localser.port)
          self.dumpInfo("E\tReboot")
          time.sleep(1)
          os.kill(os.getpid(), signal.SIGUSR1)
        self.log.debug(".")

        time.sleep(5)
    """

    def comportThread(self):
        self.log.debug("Lora Thread")
        # serial server

        while True:
          try:
            self.log.debug("Trying to open com port " + str(self.args.comport))
            ser = serial.Serial\
                    (
                        port=self.args.comport,# '/dev/ttyUSB1',
                        baudrate=38400,
                        parity=serial.PARITY_NONE,
                        stopbits=serial.STOPBITS_ONE,
                        bytesize=serial.EIGHTBITS,
                        timeout=5
                    )

            # ser.open()
            if not ser.isOpen():
                raise Exception("Cannot open port " + str(self.args.comport))

            self.dumpInfo("L\tConnected to " + ser.port)

            time.sleep(.3)

            """
            self.thwatchdog = threading.Thread(target=self.watchdogThread, args=[ser])
            self.thwatchdog.daemon = True
            self.thwatchdog.start()
            """

            ser.flushOutput()
            ser.flushInput()

            time.sleep(.3)

            self.loraTransact(ser, "AT+RF=ON") # OK Set the RF ON
            self.loraTransact(ser, "AT+RFRX=SET,LORA,,125000,7") # OK Set some RX parameters. Let the channel to default frequency
            self.loraTransact(ser, "AT+RFTX=SET,LORA,868100000,14,125000,7")

            time.sleep(.5)

            self.rxEnabler(ser, False)
            self.rxEnabler(ser, True)

            ser.flushOutput()
            ser.flushInput()

            # …..  Wait RX (set module B)
            # +RFRX: -78.00,3.00,0,152987007,CAFE
            # receive 0xCAFE hexa frame, rssi -78, snr 3, at timestamp 152987007 ms
            # A la fin éventuellement, AT+RFRX=STOP  Stop continuous Rx

            before = time.time()
            self.log.debug("Waiting data from comport")
            while True and os.path.exists(ser.port):
                r_str = ""
                try:
                  if self.receptionQueue.empty():
                    r = ser.readline()
                    if r:
                      r_str = r.decode('utf-8').strip()
                  else:
                    self.log.info("****************** saved by comm queue")
                    r_str = self.receptionQueue.get()
                except serial.SerialException as e:
                  pass
                except Exception as e:
                    self.dumpInfo("E\t" + str(e))
                    self.log.error(str(e))

                """
                if r_str == "OK" or r_str == "ERROR":
                  r_str = ""
                """

                if r_str:
                    self.log.debug("Just receiving >" + r_str)
                    if r_str.startswith("+RFRX:") :
                        #eliminons le +RFRX
                        r_str = r_str[6:].strip()
                        try:
                            # cherchons la dernière ,
                            datas = r_str.split(',')
                            self.log.debug("RSSI:" + datas[0])
                            self.localRSSI = datas[0]
                            self.localSigPoste = 0
                            self.localSigLevel = self.localRSSI

                            self.log.debug("SNR:" + datas[1])
                            self.log.debug("CRCerror:" + datas[2])
                            self.log.debug("Timestamp:" + datas[3] + " ms")
                            self.log.debug("Content:" + datas[4])

                            info = "\t".join(datas)

                            if datas[4][0:8] == '4C4F473A':
                              # cherchons le \r\n
                              ip = datas[4].find('0D0A')
                              if ip > -1:
                                self.log.info(binascii.unhexlify(datas[4][0:ip]).decode().strip())
                            else:
                              self.workWithReceivedData(datas[4], info)

                            # print(termcolor.colored(str(datatosend),'green'))
                        except Exception as e:
                            self.dumpInfo("E\t" + str(e))
                            self.log.error(str(e))
                    else:
                        self.log.error("Don't start with +RFRX: !")
                else:
                    maintenant = time.time()
                    if maintenant - before > 10:
                      before = maintenant
                      self.docommand()
                      tosend = None
                      if len(self.commandToDo) > 0:
                        cmd = self.commandToDo.pop(0)
                        self.log.info("Running command " + cmd)
                        if cmd == 'INSTSTART':
                          self.installMode = True
                        elif cmd.startswith('INSTDONE'):
                          if cmd == 'INSTDONE**':
                            self.installMode = False
                          tosend = cmd
                        else:
                          tosend = cmd

                      if not tosend and self.installMode:
                        tosend = 'TOPNOW'

                      if tosend:
                        self.log.debug("Message:" + tosend)

                        self.rxEnabler(ser, False)

                        self.loraTransact(ser, "AT+RFTX=SNDTXT," + tosend + ",1")#sndtxt code en hexa tout seul

                        self.rxEnabler(ser, True)
          except Exception as e:
            self.log.error(str(e) + "\nWaiting...before retrying")
            time.sleep(5)
          else:
            self.log.debug("End of LoRa")
            try:
              ser.close()
              self.log.debug("closed !")
            except Exception as e:
              pass

        try:
          ser.close()
        except Exception as e:
          pass

    def mreadline(self, ser):
        done = False
        data = bytearray()
        try:
            while not done:
                c = ser.read(1)
                if not c:
                    done = True
                else:
                    data += c
        except Exception as e:
            self.log.error(str(e))

        return binascii.hexlify(data).upper()

    def recportThread(self):
        self.log.debug("Com mode")
        # serial server

        self.log.debug("Trying to open com port " + str(self.args.recport))
        try:
            # configure the serial connections (the parameters differs on the device you are connecting to)
            ser = serial.Serial\
                    (
                        port=self.args.recport,# '/dev/ttyUSB1',
                        baudrate=4800,
                        parity=serial.PARITY_NONE,
                        stopbits=serial.STOPBITS_ONE,
                        bytesize=serial.EIGHTBITS,
                        timeout=1,
                        interCharTimeout=0.3
                    )

            # ser.open()
            if not ser.isOpen():
                raise Exception("Cannot open port " + str(self.args.comport))

            ser.flushOutput()
            ser.flushInput()

            while 1:
                self.log.debug("Waiting data from recport")
                r = self.mreadline(ser)
                r_str = r.decode('utf-8').strip()
                if r_str:
                    self.log.debug("Just receiving >" + r_str)
                    try:
                        self.workWithReceivedData(r_str, r_str)
                    except Exception as e:
                        self.log.error(str(e))

            ser.close()
        except Exception as e:
            self.dumpInfo("E\t" + str(e))
            self.log.error(str(e))

    def srrportThread(self):
        self.log.debug("Srr mode")
        # serial server

        self.log.debug("Trying to open com port " + str(self.args.srrport))
        try:
            # configure the serial connections (the parameters differs on the device you are connecting to)
            ser = serial.Serial\
                    (
                        port=self.args.srrport,# '/dev/ttyUSB1',
                        baudrate=38400,
                        parity=serial.PARITY_NONE,
                        stopbits=serial.STOPBITS_ONE,
                        bytesize=serial.EIGHTBITS,
                        timeout=1,
                        interCharTimeout=0.3
                    )

            # ser.open()
            if not ser.isOpen():
                raise Exception("Cannot open port " + str(self.args.srrport))

            ser.flushOutput()
            ser.flushInput()

            while 1:
                self.log.debug("Waiting data from srrport")
                r = self.mreadline(ser)
                r_str = r.decode('utf-8').strip()
                if r_str:
                    self.log.debug("Just receiving >" + r_str)
                    try:
                        self.workWithReceivedData(r_str, r_str)
                    except Exception as e:
                        self.log.error(str(e))

            ser.close()
        except Exception as e:
            self.dumpInfo("E\t" + str(e))
            self.log.error(str(e))

    def radioupdate(self, idsender, idreceiver, senderbattery, rxlevel, status):
        done = False
        try:
            self.log.debug("Socket creation")
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.log.debug("Socket connection")
            sock.connect((HOST, PORT))
            self.log.debug("Socket sending")
            parameters="?idsender="+str(idsender)+"&idreceiver="+str(idreceiver)+"&senderbattery="+str(senderbattery)+"&rxlevel="+str(rxlevel)+"&status="+str(status)
            sock.send(("GET %s%s HTTP/1.0\r\nHost: %s\r\n\r\n" % (GET,parameters,HOST)).encode())
            self.log.debug("Socket receiving")
            data = sock.recv(1024)
            reply = ""
            while len(data):
                reply = reply + data.decode()
                done = done or "200 OK" in reply
                data = sock.recv(1024)
            self.log.debug("Socket closing")
            sock.close()
            self.log.debug("Reply is " + str(reply).strip())
            self.log.info("Reply status is " + str(done))
        except Exception as e:
            self.log.error(str(e))
            self.dumpInfo("E\t" + str(e))
        return done

    def run(self):
        """
        if self.args.codetime is None:
            raise Exception("A codetime must be selected (-t)")
        if self.args.codenumber is None:
            raise Exception("A codenumber must be selected (-c)")
        if self.args.sicardno is None:
            raise Exception("A sicardno must be selected (-s)")
        """

        self.getip()

        self.logParam("Host", str(self.args.ip) + ":" + str(self.args.port))
        self.logParam("Verbosity", str(self.args.verbose))
        self.logParam("Time", str(self.args.codetime))
        self.logParam("Code", str(self.args.codenumber))
        self.logParam("SICard", str(self.args.sicardno))
        self.logParam("Decode", str(self.args.decode))
        self.logParam("Debugfile", str(self.args.debugfile))

        if self.args.simulateget:
            self.log.info("Sending GET request")
            self.radioupdate(0,1,3547,-100,0)
            self.radioupdate(0,2,3700,-110,0)
            self.radioupdate(1,2,3530,-80,1)
            self.radioupdate(2,3,3580,-124,0)
            self.radioupdate(4,0,3680,-95,0)
            self.radioupdate(3,4,3680,-60,0)
            exit(1)

        if self.args.simulatemultipleget:
            self.log.info("Sending multiple GET request")

            for i in range(10):
                self.radioupdate(i+60,1+i,1000+i,-100+i,0)

            exit(1)

        if self.args.debugfile and os.path.exists(self.args.debugfile):
          try:
            ftime_sec = os.path.getmtime(self.args.debugfile)
            ftime_str = time.strftime("%Y%m%d@%H%M%S",time.localtime(ftime_sec))

            filename, file_extension = os.path.splitext(self.args.debugfile)


            self.log.info("Copy " + self.args.debugfile + " to " + filename + ftime_str + file_extension)
            shutil.copy2(self.args.debugfile, filename + ftime_str + file_extension)
            os.remove(self.args.debugfile)
          except Exception as e:
            self.log.error(str(e))
            pass

        self.dumpInfo("L\tSendpunch started")

        if self.args.decode is not None:
            self.log.debug("Decode mode")
            datatosend = self.decode_punch(self.args.decode, "")
            self.sendto(datatosend)
        else:
            if self.args.comport is not None:
                self.dumpInfo("L\tStarting port " + self.args.comport)
                self.thcomport = threading.Thread(target=self.comportThread)
                self.thcomport.daemon = True
                self.thcomport.start()

            if self.args.recport is not None:
                self.dumpInfo("L\tStarting port " + self.args.recport)
                self.threcport= threading.Thread(target=self.recportThread)
                self.threcport.daemon = True
                self.threcport.start()

            if self.args.srrport is not None:
                self.dumpInfo("L\tStarting port " + self.args.srrport)
                self.thsrrport= threading.Thread(target=self.srrportThread)
                self.thsrrport.daemon = True
                self.thsrrport.start()

            if self.thcomport:
                self.log.debug("Waiting for end of comport thread")
                self.thcomport.join()
            if self.threcport:
                self.log.debug("Waiting for end of recport thread")
                self.threcport.join()
            if self.thsrrport:
                self.log.debug("Waiting for end of srrport thread")
                self.thsrrport.join()

            self.log.debug("Done !")

        """
        else:
            self.log.debug("Direct mode")
            if self.args.codetime is None:
                raise Exception("A codetime must be selected (-t)")
            if self.args.codenumber is None:
                raise Exception("A codenumber must be selected (-c)")
            if self.args.sicardno is None:
                raise Exception("A sicardno must be selected (-s)")
            _type = 0
            _codeday = 0
            datatosend = struct.pack('<BHlLL',int(_type), int(self.args.codenumber), int(self.args.sicardno), int(_codeday), int(self.args.codetime))
            self.sendto(datatosend)
        """

        return 0

    def sendto(self, datatosend):
        if datatosend is not None:
            try:
                self.getip()

                self.log.debug("Data to send {0}:{1}".format(str(datatosend), str(len(datatosend))))

                self.socket = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
                self.socket.connect((str(self.args.ip) , self.args.port))
                v = self.socket.send(datatosend)
                self.log.debug("Send reply " + str(v))
                self.socket.close()

                self.sent += 1
                self.log.info("Packet sent:" + str(self.sent))
            except Exception as e:
                self.dumpInfo("E\t" + str(e))
                self.log.error(str(e))

    def title(self, s):
        self.log.info(s)

    def logParam(self, s, p):
        self.log.debug(s.ljust(40, ".") + str(p))

    def computeCRC53(self, u0, u1, u2, u3, u4):
        """
        Calcul d'un CRC16 bits
        Les variables sont 'arrondies' avec %=10000 pour simuler le fonctionnement des unsigned short du C
        :param u0:
        :param u1:
        :param u2:
        :param u3:
        :param u4:
        :return:
        """
        tmp = u0
        bs = [u1, u2, u3, u4, 0] # attention, il y a une valeur en plus à 0
        for val in bs:
            for j in range(16):
                if tmp & (1 << 15):
                    tmp *= 2
                    if val & (1 << 15):
                        tmp += 1
                    tmp ^= 0x8005
                    tmp %= 0x10000
                else:
                    tmp *= 2
                    if val & (1 << 15):
                        tmp += 1
                    tmp %= 0x10000
                val *= 2
                val %= 0x10000
        return tmp

    def computeCRCD3(self, u0, u1, u2, u3, u4, u5, u6, u7):
        """
        Calcul d'un CRC16 bits
        """
        tmp = u0
        bs = [u1, u2, u3, u4, u5, u6, u7]
        for val in bs:
            for j in range(16):
                if tmp & (1 << 15):
                    tmp <<= 1
                    if val & (1 << 15):
                        tmp += 1
                    tmp ^= 0x8005
                else:
                    tmp <<= 1
                    if val & (1 << 15):
                        tmp += 1
                val <<= 1
        tmp &= 0xffff
        return tmp

    #02D30D00250002F25D3302C3EE0004E0E9AF03    -  3A010E96
    # 0 1 2 3 4 5 6 7 8 9101112131415161718
    def decodeD3(self, packed_data):#, loginfo):
        self.log.debug("Decoding " + str(len(packed_data)))
        length = packed_data[2]

        if length != 13:
            raise Exception("Expecting a size of 13 bytes instead of " + str(length))
        if packed_data[18] != 0x03:
            raise Exception("Packet must have 03 at the end")

        posteid = packed_data[3] * 256 + packed_data[4]
        self.log.debug("PosteID=" + str(posteid))

        cardser = packed_data[5]

        if cardser == 0:
            cns = packed_data[6]
            if cns < 5:
                cardnr = 100000 * cns + packed_data[7] * 256 + packed_data[8]
            else:
                cardnr = (packed_data[6] * 256 + packed_data[7]) * 256 + packed_data[8]
        else:
            cardnr = (packed_data[6] * 256 + packed_data[7]) * 256 + packed_data[8]

        self.log.debug("CardNr=" + str(cardnr))

        dayinfo = packed_data[9]
        time_sec = packed_data[10]*256 + packed_data[11] + float(packed_data[12]) / 256
        if dayinfo & 1:# impair => +12h
            self.log.debug("PM Mode, sub is " + str(int(packed_data[12])) + "=>" + str(float(packed_data[12]) / 256))
            time_sec += 12*3600

        self.log.debug("Time(sec)=" + str(time_sec))

        _type = 0
        _codeday = 0
        _CRC = packed_data[16]*256 + packed_data[17]
        self.log.debug("_CRC=" + str(_CRC))

        CRC = self.computeCRCD3(
                packed_data[1]*256 + packed_data[2],
                packed_data[3]*256 + packed_data[4],
                packed_data[5]*256 + packed_data[6],
                packed_data[7]*256 + packed_data[8],
                packed_data[9]*256 + packed_data[10],
                packed_data[11]*256 + packed_data[12],
                packed_data[13]*256 + packed_data[14],
                packed_data[15]*256 + 0
            )

        if int(CRC) != int(_CRC):
            raise Exception("CRC computation error, expecting " + str(_CRC) + " but compute " + str(CRC))

        datatosend = struct.pack('<BHlLL',int(_type), int(posteid), int(cardnr), int(_codeday), int(time_sec*10))

        #self.dumpInfo("P\t" + loginfo + "\t" + str(posteid)+ "\t" + str(cardnr))
        """
        if self.args.debugfile is not None:
            with open(self.args.debugfile, "a") as myfile:
                myfile.write(str(datetime.datetime.now()) + "\t" + loginfo + "\t" + str(posteid)+ "\t" + str(cardnr) + "\r\n")
        """
        self.localPosteID = posteid
        self.localCardNR = cardnr

        return datatosend, 19

    def decode53(self, packed_data):#, loginfo):
        index = 2 #
        PTWD, index = getnextbyte(packed_data, index)
        CSI, index = getnextbyte(packed_data, index)
        SNS, index = getnextbyte(packed_data, index)
        SN1, index = getnextbyte(packed_data, index)
        SN0, index = getnextbyte(packed_data, index)
        QL, index = getnextbyte(packed_data, index)
        PTH, index = getnextbyte(packed_data, index)
        PTL, index = getnextbyte(packed_data, index)
        PT0, index = getnextbyte(packed_data, index)
        CRC1, index = getnextbyte(packed_data, index)
        CRC0, index = getnextbyte(packed_data, index)

        _PT = PTH * 256 + PTL
        _SN = (((SNS * 256) + SN1) * 256) + SN0
        _CRC = CRC1 * 256 + CRC0

        self.log.debug("ID=" + str(ID))
        self.log.debug("PTWD=" + str(PTWD))
        self.log.debug("CSI=" + str(CSI))
        self.log.debug("SNS=" + str(SNS))
        self.log.debug("SN1=" + str(SN1))
        self.log.debug("SN0=" + str(SN0))
        self.log.debug("QL=" + str(QL))
        self.log.debug("PTH=" + str(PTH))
        self.log.debug("PTL=" + str(PTL))
        self.log.debug("PT0=" + str(PT0))

        self.log.debug("_PT=" + str(_PT))
        self.log.debug("_SN=" + str(_SN))
        self.log.debug("_CRC=" + str(_CRC))

        _type = 0
        _codeday = 0
        _codetime_sec = _PT + PT0 * 0.05 # à vérifier quand même

        self.log.debug("CSI=" + str(CSI))
        self.log.debug("SN=" + str(_SN))

        CRC = self.computeCRC53(ID*256 + PTWD, CSI*256 + SNS, SN1*256 + SN0, QL * 256 + PTH, PTL * 256 + PT0)

        if int(CRC) != int(_CRC):
            raise Exception("CRC computation error, expecting " + str(_CRC) + " but compute " + str(CRC))


        # zone de test
        # CSI = int(self.args.codenumber)
        # v = int(self.args.codetime)
        # v += 3
        # _codetime_sec = v
        # self.args.codetime = str(v)
        # zone de test

        datatosend = struct.pack('<BHlLL',int(_type), int(CSI), int(_SN), int(_codeday), int(_codetime_sec*10))

        #self.dumpInfo("P\t" + loginfo + "\t" + str(CSI)+ "\t" + str(_SN))
        """
        if self.args.debugfile is not None:
            with open(self.args.debugfile, "a") as myfile:
                myfile.write(str(datetime.datetime.now()) + "\t" + loginfo + "\t" + str(CSI)+ "\t" + str(_SN) + "\r\n")
        """
        self.localPosteID = CSI
        self.localCardNR = _SN

        #datatosend = struct.pack('<BHlLL',int(_type), int(self.args.codenumber), int(self.args.sicardno), int(_codeday), int(self.args.codetime))
        return datatosend, index

    def updateInfo(self, idsender, idreceiver, senderbattery, rxlevel, status):
        self.radioupdate(idsender, idreceiver, senderbattery, rxlevel, status)

        self.dumpInfo('S' + "\t" + str(idsender) + "\t" + str(idreceiver) + "\t" + str(rxlevel))
        self.dumpInfo('B' + "\t" + str(idsender) + "\t" + str(senderbattery) + "\t" + str(status))

    def decodeSignalInfo(self, r_str):
        self.log.debug("decoding signal info:" + r_str)
        poste = int(r_str[0:2], 16)
        level = -int(r_str[2:6], 16)

        self.log.info("id:" + str(poste) + " SignalLevel:" + str(level) + " dB")

        self.localSigPoste = poste
        self.localSigLevel = level

        self.updateInfo(self.localBatPoste, self.localSigPoste, self.localBatLevel, self.localSigLevel, self.localStatus)

        datatosend = None
        index = 9
        return datatosend, index

    def decodeBatteryInfo(self, r_str):
        self.log.debug("decoding battery info:" + r_str)
        poste = int(r_str[0:2], 16)
        level = int(r_str[2:6], 16)
        status = int(r_str[6:8], 16)

        self.log.info("id:" + str(poste) + " BatteryLevel:" + str(level) + " mV" + " status:" + str(status))

        self.localBatPoste = poste
        self.localBatLevel = level
        self.localStatus = status

        datatosend = None
        index = 9
        return datatosend, index

    def decode_punch(self, datatodecode):#, loginfo):
        """
        :param datatodecode: a string with hexadecimal content
        :return: a string ready to send to meos

        Send out punch data 2 (get punch 2)
        STX, 53h, PTWD, CSI, SNS, SN1, SN0, QL, PTH, PTL, PT0,CRC1, CRC0, ETX

        PTWD  punching time TWD value
        CSI      control station identifier
        SNS     start number, series / card number
        SN1     start number, high byte / card number
        SN0     start number, low byte / card number
        QL       queue length (only for extended start, extended finish)
        PTH     punching time TH value
        PTL      punching time TL value
        PT0      punching time T0 value (0,05 sec)
        CRC1   cyclic redundancy check
        CRC0    cyclic redundancy check
        """

        if datatodecode is None:
            raise Exception("Cannot decode None data")

        #if len(datatodecode) < 28:
        #    raise Exception("Data must be at least 28 char bytes long instead of " + len(datatodecode))

        self.log.debug("Decoding >" + str(datatodecode))

        datatosend = None
        datalastindex = 0
        # testons si il ne fait pas partie des 10 derniers paquets déja traité, si c'est le cas on l'élimine
        if datatodecode in self.punchMemory:
            self.log.info("Data already there ! : memory is " + str(self.punchMemory))
        else:
            self.punchMemory.append(datatodecode)
            while(len(self.punchMemory) > 10):
                self.punchMemory.pop(0)

            packed_data = binascii.unhexlify(datatodecode)
            datasize = len(packed_data)

            __type = None
            __posteid = None
            __cardnr = None
            __codeday = None
            __time10sec = None

            if packed_data[0] is not 2:
                raise Exception("Data must start with 0x02")

            if packed_data[1] is not 0x53 and packed_data[1] is not 0xD3:
                raise Exception("Punch data packet must start with 0x53 or 0xD3")

            #if packed_data[1] is 0xD3:
            #if packed_data[datasize-1] is not 3:
            #    raise Exception("Data must end with 0x03")

            if packed_data[1] is 0x53:
                datatosend, datalastindex = self.decode53(packed_data)
            elif packed_data[1] is 0xD3:
                datatosend, datalastindex = self.decodeD3(packed_data)

        return datatosend, datalastindex*2



def main(argvs=None):

    if argvs is None:
        argvs = sys.argv[1:]

    try:
        app = MyApplication(argvs)
        return app.run()
    except SystemExit:
        # gestion de l'exception 'normale' quand on fait un -h (pour l'aide) dans la ligne de commande
        return 0
    except ExitCommand:
        return 1
    except Exception as e:
        print(termcolor.colored(str(e),'red'))
        # print(colored(str(e), 'red'))
        traceback.print_exc(file=sys.stdout)

        return 2

def _test():
    verbose = "-T" in sys.argv

    global tries, failures

    result = doctest.testmod(verbose=verbose,  report=0)

    failures = int(result[0])
    tries = int(result[1])

    if failures is not 0:
        doctest.master.summarize(verbose=verbose)

    return failures


if __name__ == "__main__":
    signal.signal(signal.SIGINT, signal_handler)
    title = os.path.splitext(__file__)[0] + " @ " + socket.gethostname() + " v" + __version__ + " " + __authors__ + " " + __contact__

    print(termcolor.colored('_'*len(title), 'white'))
    print(termcolor.colored(title, 'cyan'))

    if _test() is not 0:# arret si des erreurs de tests sont détectés
        print(termcolor.colored("Ko",'red'))
        sys.exit(3)

    sys.exit(main(sys.argv[1:]))
