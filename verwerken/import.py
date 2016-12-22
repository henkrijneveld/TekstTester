import json
from pprint import pprint
from Crypto.Cipher import AES

print "Starting import"

def extractip(s):
    base64 = s.replace("-", "/")


    pass

with open("teksttesterp-export.json") as fp:
    data = json.load(fp)["result"]

    for key, content in data.iteritems():
        extractip(key)


#        pprint(key)
#        pprint(content)


