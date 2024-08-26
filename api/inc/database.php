<?php
// CONNECTION
try {
    $dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
} catch (PDOException $e) {
    // tentar reconectar após algum intervalo, por exemplo
}

$stmt = $dbh->prepare("INSERT INTO REGISTRY (name, value) VALUES (?, ?)");
$stmt->bindParam(1, $name);
$stmt->bindParam(2, $value);

// insere uma linha
$name = 'one';
$value = 1;
$stmt->execute();

// insere outra linha com valores diferentes
$name = 'two';
$value = 2;
$stmt->execute();
?>