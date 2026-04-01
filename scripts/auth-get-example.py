# -*- coding: utf-8 -*-
import base64
import datetime
import hashlib
import hmac
import json
from urllib.parse import urlparse
from urllib.parse import quote
from urllib.parse import unquote
from urllib.parse import parse_qs

import requests

# Application ApiAppKey
ApiAppKey = 'Your ApiAppKey'
# Application ApiAppSecret
ApiAppSecret = 'Your ApiAppSecret'

# APIGW access address
# ###Special Character Test --->>>>> %23%23%23Special Character Test
# xxxxxx.com Please use the real Pudu Open Platform fixed public domain: e.g. test environment open-platform-test.pudutech.com
Url = "https://xxxxxx.com/pudu-entry/data-open-platform-service/v1/api/healthCheck?b=${b}&a=${a}&c=${c}"
Url = Url.replace("${b}", quote("2"))
Url = Url.replace("${a}", quote("###Special Character Test"))
Url = Url.replace("${c}", quote("3"))

HTTPMethod = "GET"  # method
Accept = "application/json"
ContentType = "application/json"

urlInfo = urlparse(Url)
Host = urlInfo.hostname
Path = urlInfo.path

# Signature path does not include environment information
if Path.startswith(("/release", "/test", "/prepub")):
    Path = "/" + Path[1:].split("/", 1)[1]
Path = Path if Path else "/"

def normalize_query(query: str) -> str:
    if not query:
        return ""

    # parse_qs will merge duplicate keys into a list
    query_dict = parse_qs(query, keep_blank_values=True)
    new_params = []

    for key in sorted(query_dict.keys()):
        values = query_dict[key]
        # Clean empty strings
        cleaned_values = [v for v in values if v != ""]
        if cleaned_values:
            # Merge multiple values
            new_params.append(f"{key}=" + ",".join(cleaned_values))
        else:
            # Empty value keeps only key
            new_params.append(key)

    return "&".join(new_params)

# Concatenate query parameters, query parameters need to be sorted lexicographically
if urlInfo.query:
    Path = Path + "?" + normalize_query(unquote(urlInfo.query))


ContentMD5 = ""
GMT_FORMAT = "%a, %d %b %Y %H:%M:%S GMT"
xDate = datetime.datetime.utcnow().strftime(GMT_FORMAT)

# Modify body content
if HTTPMethod == "POST":
     body = {}
     body_json = json.dumps(body)
     body_md5 = hashlib.md5(body_json.encode()).hexdigest()
     ContentMD5 = base64.b64encode(body_md5.encode()).decode()

# Get signature string
signing_str = "x-date: %s\n%s\n%s\n%s\n%s\n%s" % (
    xDate,
    HTTPMethod,
    Accept,
    ContentType,
    ContentMD5,
    Path,
)

# Calculate signature
sign = hmac.new(ApiAppSecret.encode(), msg=signing_str.encode(), digestmod=hashlib.sha1).digest()
sign = base64.b64encode(sign).decode()
auth = "hmac id=\"" + ApiAppKey + "\", algorithm=\"hmac-sha1\", headers=\"x-date\", signature=\""
sign = auth + sign + "\""


# Send request
headers = {"Host": Host, "Accept": Accept, "Content-Type": ContentType, "x-date": xDate, "Authorization": sign}

print(Url)
print(headers)


if HTTPMethod == "GET":
    ret = requests.get(Url, headers=headers)
if HTTPMethod == "POST":
    ret = requests.post(Url, headers=headers, data=body_json)

print(ret.headers)
print(ret.text)