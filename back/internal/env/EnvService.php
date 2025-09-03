<?php

class EnvService
{
    public static function loadEnv(): array
    {
        $envs = [];
        $path = __DIR__ . "/../../../.env";
        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('O arquivo de ambiente "%s" não foi encontrado ou não pode ser lido.', $path));
        }

        // Abre o arquivo para leitura
        $arquivo = fopen($path, 'r');
        if ($arquivo) {
            try {
                // Lê o arquivo linha por linha
                while (($linha = fgets($arquivo)) !== false) {
                    $linha = trim($linha);

                    // Ignora linhas de comentário e linhas em branco
                    if (empty($linha) || str_starts_with($linha, '#')) {
                        continue;
                    }

                    // Separa a chave do valor
                    $partes = explode('=', $linha, 2);

                    if (count($partes) !== 2) {
                        continue; // Ignora linhas mal formatadas
                    }

                    $chave = trim($partes[0]);
                    $valor = trim($partes[1]);

                    $envs[$chave] = $valor;
                }
            } catch (Throwable $e) {
                throw $e;
            } finally {
                fclose($arquivo);
                return $envs;
            }
        }
    }
}
