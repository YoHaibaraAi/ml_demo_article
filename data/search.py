#coding=utf-8
import pandas as pd
import os
import sys
from sklearn.feature_extraction.text import  CountVectorizer,TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.grid_search import GridSearchCV
import string
import chardet
import types
reload(sys)
sys.setdefaultencoding('utf-8')

def trainMethod(train):
    X_train=[]
    for i in train['words']:
        X_train.append(' '.join(i))
    y_train=train['is_ad']
    pip_count=Pipeline([('count_vec',CountVectorizer(analyzer='word')),('mnb',MultinomialNB())])
    pip_tfidf=Pipeline([('tfidf_vec',TfidfVectorizer(analyzer='word')),('mnb',MultinomialNB())])
    params_count={'count_vec_binary':[True,False],'count_vec_ngram_range':[(1,1),(1,2)],'mnb_alpha':[0.1,1.0,10]}
    params_tfidf={'tfidf_vec_binary':[True,False],'tfidf_vec_ngram_range':[(1,1),(1,2)],'mnb_alpha':[0.1,1.0,10]}
    gs_count=GridSearchCV(pip_count,params_count,cv=4,n_jobs=-1,verbose=1)
    gs_count.fit(X_train,y_train)
    print gs_count.best_score_
    print gs_count.best_params_

    gs_tfidf=GridSearchCV(pip_tfidf,params_tfidf,cv=4,n_jobs=-1,verbose=1)
    gs_tfidf.fit(X_train,y_train)
    print gs_tfidf.best_score_
    print gs_tfidf.best_params_







if __name__=='__main__':
    bathPath=os.getcwd()
    train = pd.read_csv(bathPath+'/data.csv')
    # test =pd.read_csv(bathPath+'/data.csv')
    trainMethod(train)
    #print train.head()