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
def readFile(filename,basePath,shot_name):
    fopen=open(filename,'r')
    for eachLine in fopen:
         cutWord(eachLine,basePath,shot_name)
    fopen.close()
def cutWord(str_content,basePath,shot_name):
    seg_list = jieba.cut(str_content, cut_all=False)
    nice = " ".join(seg_list)

    cut_path=basePath+'/cut/'+shot_name+".txt"
    ad_path=basePath+'/ad/'+shot_name+".txt"
    nice=nice.translate(("",string.punctuation))

    nice=''.join(re.findall(u'[\u4e00-\u9fff|\u0020]+', nice))

    c=Counter(nice.split(" "))
    brand_index=0
    brand_file=open(basePath+"/brand.txt")
    brand_lines=brand_file.read()
    brand_lines=brand_lines.split('\n')
    #print brand_lines
    # for brand_line in brand_lines:
    #     print brand_line
    brand_file.close()

    file_obj = open(cut_path, 'w')
    for key_line in c:

        if key_line in brand_lines:
            brand_index=brand_index+1
        line= key_line+" "+str(c[key_line])

        file_obj.write(line)
    file_obj.close()
    if brand_index>0:
        os.rename(cut_path,ad_path)


def eachFile(filepath,basePath):
    pathDir=os.listdir(filepath)


    #print os.walk(filepath)
    for allDir in pathDir:
        child =os.path.join('%s%s' %(filepath,allDir))
        if(os.path.splitext(child)[1]!=''):
            file_name = os.path.split(child)[1]
            shot_name = os.path.splitext(file_name)[0]
            readFile(child,basePath,shot_name)

if __name__=='__main__':
    basePath=os.getcwd()
    contentPath=basePath+'/content/'
    eachFile(contentPath,basePath)
    print "执行完成"

