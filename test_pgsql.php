<?php
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=NaRegua;", "Bucheb", "143964");
    echo "✅ Conectou com sucesso ao banco PostgreSQL!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar: " . $e->getMessage();
}