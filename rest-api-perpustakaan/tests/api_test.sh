#!/bin/bash

BASE_URL="http://localhost:8000/api/books"

echo "== Test 1: GET semua buku =="
curl -s $BASE_URL
echo -e "\n"

echo "== Test 2: POST buku baru =="
RESPONSE=$(curl -s -X POST $BASE_URL \
  -H "Content-Type: application/json" \
  -d '{"judul":"Buku Test","penulis":"Penulis Test","tahun":2024,"stok":5}')
echo "$RESPONSE"

NEW_ID=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)

if [ -z "$NEW_ID" ]; then
    NEW_ID=8
fi
echo "ID baru: $NEW_ID"
echo ""

echo "== Test 3: GET buku ID $NEW_ID =="
curl -s "$BASE_URL/$NEW_ID"
echo -e "\n"

echo "== Test 4: PUT ubah buku =="
curl -s -X PUT "$BASE_URL/$NEW_ID" \
  -H "Content-Type: application/json" \
  -d '{"judul":"Buku Test Revisi","penulis":"Penulis Test","tahun":2024,"stok":10}'
echo -e "\n"

echo "== Test 5: DELETE buku =="
curl -s -X DELETE "$BASE_URL/$NEW_ID"
echo -e "\n"
echo "== Test 6: Validasi error =="
curl -s -X POST $BASE_URL \
  -H "Content-Type: application/json" \
  -d '{"judul":""}'
echo -e "\n"