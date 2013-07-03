#!/usr/bin/env python
# -*- coding: utf-8 -*-
import re

class Stemming:

    def __init__(self):
        self.v = '(?:[aeiouyäöü])';                 # the vowels
        self.c = '(?:[^aeiouyäöü])';                # the non-vowels (consonants)
        self.s_ending = '(?:[bdfghklmnrt])';        # valid s-ending predecessors
        self.st_ending = '(?:[bdfghklmnt])';        # valid st-ending  predecessors
        self.word = '';
        self.r1 = '';
        self.p1 = 0;
        self.r2 = '';
        self.p2 = 0;
        
    def stem(self,word):
    
        if len(word) <= 2:
            return self.postlude(word);
        self.word = self.prelude(word.strip())

        # Setting up R1 und R2 (quoting original description):
        # "R1 is the region after the first non-vowel following a vowel, or  is the null region at
        # the end of the word if there  is no such non-vowel.
        # R2 is the region after the first non-vowel following a vowel in  R1, or is the null
        # region at the  end of the word if there is no such non-vowel."
                    
        # Note: If R1 is a null region it is not necessary to proceed because none of the further
        # steps will have matching patterns. The stem function then will be return immediately after
        # executing the postlude part.
        p=re.compile(self.v+""+self.c+"(.*)$",re.U)
        match=p.search(self.word)
        if not match==None:
            self.p1 = match.start(1) 
            if self.p1<3:
                self.p1=3
            self.r1 = self.word[self.p1:]
        else:
            self.p1=0
            self.r1=""
            return self.postlude(self.word)

        p= re.compile(self.v+"("+self.c+".*)$",re.U)
        match=p.search(self.r1)
        if not match==None :
            self.p2 = match.start(1)+self.p1
            self.r2 = match.group(1)
        else:
            self.p2=0
            self.r2=""
            

        self.step_1();

        self.step_2();

        if self.p2 == 0:
            return self.postlude(self.word);

        self.step_3();

        return self.postlude(self.word);
        


        
    def step_1(self):
        self.word = re.sub(r"^(.{"+str(self.p1)+",})(?:e|em|en|ern|er|es)$", r"\1", self.word)
        if re.match("(?:s)$", self.r1) and re.match(self.s_ending+"(?:s)$", self.word):
            self.word =  self.word[0:len(self.word)-1]
    
    def step_2(self):
        self.word = re.sub(r"^(.{"+str(self.p1)+",})(?:en|er|est)$", r'\1', self.word)
        if (re.match("st", self.r1) and re.match(".{3,}"+self.st_ending+"(st)$", self.word)): 
            self.word = self.word[0:len(self.word)-2]
        
    


    def step_3(self): 
        if (re.search("(end|ung)", self.r2)):
            self.word = re.sub(r'^((.*)[(ig)])(?:end|ung)$',r'\1', self.word)
            self.word = re.sub(r'^((.*)[^e])(?:end|ung)$',r'\1', self.word);
     
        if (re.search("(ig|ik|isch)", self.r2)):
            self.word = re.sub(r'^(.*[^e])(ig|ik|isch)$',r'\1', self.word);

        if (re.search('(lich|heit)', self.r2)):
            if (re.match('(er|en)(lich|heit)', self.r1)):
                self.word = re.sub(r'^(.*)(er|en)(lich|heit)$',r'\1', self.word)
            self.word = re.sub(r'^(.*)(lich|heit)$',r'\1', self.word);

        if (re.search("keit", self.r2)):
            self.word = re.sub(r'^(.*)keit$',r'\1', self.word)
            if (re.match('(lich|ig)(keit)', self.r2)): 
                self.word = re.sub(r'^(.*)(lich|ig)$',r'\1', self.word)
            
        
    

    def prelude(self,word):
        word=re.sub(r"ß","ss",word)
        word=re.sub(r"("+self.v+")u("+self.v+")",r"\1U\2",word)
        word=re.sub(r"y("+self.v+")",r"Y\1",word)
        return word
    

    def postlude(self,word):
        word=word.replace("ä", "a")
        word=word.replace("ö", "o")
        word=word.replace("ü", "u")
        return  word.lower()
    
    