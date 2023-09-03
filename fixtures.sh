#!/bin/bash
if [ "$1" == "append" ]; then
  php bin/console d:f:l --append
else
  echo "yes" | php bin/console d:f:l
fi