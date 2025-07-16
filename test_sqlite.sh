#!/bin/bash

# Script para testar a configuração do SQLite
echo "Testando configuração do SQLite..."
echo "APP_ENV=testing" > .env.testing
echo "DB_CONNECTION=sqlite" >> .env.testing
echo "DB_DATABASE=:memory:" >> .env.testing

# Executar um teste simples
./vendor/bin/phpunit tests/Unit/Models/UserTest.php --testdox

echo "Fim do teste."
