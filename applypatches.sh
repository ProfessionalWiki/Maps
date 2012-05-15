#! /bin/bash

set -x


for i in *; do
    if [[ $i == *.patch ]]; then
	git apply $i
	git add *
	git commit -am "Applied $i from broken git repo"
    fi
done
