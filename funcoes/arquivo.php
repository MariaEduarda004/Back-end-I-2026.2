<?php

function ler_json(string $arquivo): array {
    $caminho = STORAGE . $arquivo;

    if (!is_file($caminho)) {
        return [];
    }

    $conteudo = file_get_contents($caminho);
    if ($conteudo === false) {
        throw new RuntimeException('Não foi possível ler o arquivo de dados.');
    }

    $dados = json_decode($conteudo, true);
    if (!is_array($dados)) {
        throw new RuntimeException('O arquivo de dados contém JSON inválido.');
    }

    return $dados;
}

function salvar_json(string $arquivo, array $dados): void {
    $caminho = STORAGE . $arquivo;
    $json = json_encode(
        $dados,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    if (file_put_contents($caminho, $json, LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível salvar o arquivo de dados.');
    }
}
