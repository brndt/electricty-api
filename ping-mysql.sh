#!/bin/bash

while ! make ping-mysql &>/dev/null; do
    echo "Waiting for database connection..."
    sleep 3
done