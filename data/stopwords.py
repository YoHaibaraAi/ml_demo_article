#coding=utf-8
import os
import jieba
import string
import re
import shutil
import types
from collections import Counter
import sys
reload(sys)
sys.setdefaultencoding('utf-8')


def func(args,dirname,names):
    ads=[]
    for i in names:
        if os.path.splitext(i)[1]==".txt":
            ads.append(os.path.splitext(i)[0])
    for ad_name in ads:
        ad_file=open(basePath+'/content/'+ad_name+'.txt','r+')
        for line in ad_file:
            seg_list = jieba.cut(line, cut_all=False)
            final = ''
            for seg in seg_list:
                seg = seg.encode('utf-8')
                if seg not in stopwords:
                    final += " "+seg

            final=unicode(final,"utf-8")
            final = final.translate(("", string.punctuation))

            final = ''.join(re.findall(u'[\u4e00-\u9fff|\u0020]+', final))
            final=' '.join(final.split())
            
        ad_file.close()
        writePath=basePath+'/words/'+ad_name+'.txt'
        write_file=open(writePath,'w')
        write_file.write(final)
        write_file.close()




if __name__=='__main__':
    basePath = os.getcwd()
    contentPath = basePath + '/ad/'
    stopwords_file = open(basePath + "/stopwords.txt")
    stopwords = stopwords_file.read()
    stopwords = stopwords.split('\n')
    os.path.walk(contentPath,func,(basePath,stopwords))

