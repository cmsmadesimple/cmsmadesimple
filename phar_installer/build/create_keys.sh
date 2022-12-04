#!/bin/bash
# priv.pem will contain your private key
# pub.pem will contain your public key
openssl genrsa -out priv.pem 1024
openssl rsa -in priv.pem -pubout -out pub.pem
