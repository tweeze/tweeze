import str
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
        
    def stem(word):
        
            if len(word) <= 2:
                return self.postlude(word);
            self.word = self.prelude(word.strip());

            # Setting up R1 und R2 (quoting original description):
            # "R1 is the region after the first non-vowel following a vowel, or  is the null region at
            # the end of the word if there  is no such non-vowel.
            # R2 is the region after the first non-vowel following a vowel in  R1, or is the null
            # region at the  end of the word if there is no such non-vowel."
                        
            # Note: If R1 is a null region it is not necessary to proceed because none of the further
            # steps will have matching patterns. The stem function then will be return immediately after
            # executing the postlude part.
            p=re.compile("^(?u)(.*"+self.v+""+self.c+")(?x)(.*)\$")
            if (match=p.match(self.word)) {
# GO HERE FURTHER                
                $this->p1 = strlen ($match[1]) < 3 ? 3 : strlen ($match[1]);
                $this->r1 = substr ($this->word, $this->p1);
            } else
                return $this->postlude($this->word);

            preg_match ("/^(?U)(.*$this->v$this->c.*$this->v$this->c)(?X)(.*)\$/", $this->word, $match);
            $this->p2 = strlen($match[1]);
            $this->r2 = $match[2];


            $this->step_1();

            $this->step_2();

            if ($this->p2 == 0)
                return $this->postlude($this->word);

            $this->step_3();

            return $this->postlude($this->word);
        }


        /*
            Step 1:

            Search for the longest among the following suffixes,

                (a) e   em   en   ern   er   es

                (b) s (preceded by a valid s-ending)

            and delete if in R1. (Of course the letter of the valid s-ending is not necessarily in R1)
        */
        function step_1() {
            // a
            $this->word = preg_replace ("/^(.{".$this->p1.",})(?:e|em|en|ern|er|es)\$/", '\\1', $this->word);

            // b
            if (preg_match ("/(?:s)\$/", $this->r1) && preg_match ("/".$this->s_ending."(?:s)\$/", $this->word)) {
                $this->word = substr ($this->word,0,-1);
            }
        }


        /*
            Step 2:

            Search for the longest among the following suffixes,

                (a) en   er   est

                (b) st (preceded by a valid st-ending, itself preceded by at least 3 letters)

            and delete if in R1.
        */
        function step_2() {
            // a
            $this->word = preg_replace ("/^(.{".$this->p1.",})(?:en|er|est)\$/", '\\1', $this->word);
            // b
            if (preg_match ("/st/", $this->r1) && preg_match ("/.{3,}".$this->st_ending."(st)\$/", $this->word)) {
                $this->word = substr ($this->word,0,-2);
            }
        }


        /*
        Step 3: d-suffixes

            Search for the longest among the following suffixes, and perform the action indicated.

            end   ung
                delete if in R2 if preceded by ig,
                delete if in R2 and not preceded by e

            ig   ik   isch
                delete if in R2 and not preceded by e

            lich   heit
                delete if in R2
                if preceded by er or en, delete if in R1

            keit
                delete if in R2
                if preceded by lich or ig, delete if in R2
        */
        function step_3() {
            if (preg_match ("/(end|ung)/", $this->r2)) {
                $this->word = preg_replace ('/^((.*)[(ig)])(?:end|ung)$/','\\1', $this->word);
                $this->word = preg_replace ('/^((.*)[^e])(?:end|ung)$/','\\1', $this->word);
            }


            if (preg_match ("/(ig|ik|isch)/", $this->r2)) {
                $this->word = preg_replace ('/^(.*[^e])(ig|ik|isch)$/','\\1', $this->word);
            }


            if (preg_match ('/(lich|heit)/', $this->r2)) {
                if (preg_match ('/(er|en)(lich|heit)/', $this->r1)) {
                    $this->word = preg_replace ('/^(.*)(er|en)(lich|heit)$/U','\\1', $this->word);
                }
                $this->word = preg_replace ('/^(.*)(lich|heit)$/','\\1', $this->word);
            }


            if (preg_match ("/keit/", $this->r2)) {
                $this->word = preg_replace ('/^(.*)keit$/','\\1', $this->word);
                if (preg_match ('/(lich|ig)(keit)/', $this->r2)) {
                    $this->word = preg_replace ('/^(.*)(lich|ig)$/','\\1', $this->word);
                }
            }
        }

        /*
        Prelude
        The original description says: Replace ß by ss, and put u and y  between vowels into upper case.
        The Snowball algorithm shows a difference here: It only puts u between vowels in upper case. y
        is uppercased if only followed by a vowel.
        */
        function prelude($word) {
            $search = array ("/ß/","/($this->v)u($this->v)/","/y($this->v)/");
            $replace = array ("ss","\\1U\\2","Y\\1");
            return preg_replace ($search, $replace, $word);
        }

        /*
        Postlude
        the umlaut accent will be removed from ä, ö and ü, and U and Y will be turned back into lower case.
        */
        function postlude($word) {
            $search = array ("ä","ö","ü");
            $replace = array ("a","o","u");
            return strtolower (str_replace ($search,$replace,$word));
        }
    }