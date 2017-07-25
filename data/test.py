#coding=utf-8
import jieba
import  jieba.analyse
import types
import re
import string
import sys
reload(sys)
sys.setdefaultencoding('utf-8')

final="你好,你是谁?我是你的朋友。"
#final=final.encode("utf-8")
#final=unicode(final,"utf-8")
final = final.translate(('', string.punctuation))
final = ''.join(re.findall(u'[\u4e00-\u9fff|\u0020]+', final))
print final

