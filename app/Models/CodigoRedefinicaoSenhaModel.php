<?php

require_once __DIR__ . '/../../config/Database.php';

class CodigoRedefinicaoSenhaModel
{
    private PDO $db;

    /**
     * Construtor que estabelece a conexão automaticamente.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Método responsável por cadastrar código.
     * @param string $idUsuario
     * @return string|false
     */
    public function cadastrar($idUsuario)
    {
        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));
        $codigoSimples = rand(100000, 999999);
        $valor = password_hash($codigoSimples, PASSWORD_DEFAULT);
        $dataExpiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $query = "INSERT INTO tbcodigos_redefinicao_senha (codId, fkUsuId, codValor, codExpiracao) VALUES (:uuid, :usuId, :valor, :dataExpiracao)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":usuId", $idUsuario);
        $stmt->bindParam(":valor", $valor);
        $stmt->bindParam(":dataExpiracao", $dataExpiracao);

        if ($stmt->execute()) {
            return $codigoSimples;
        }

        return false;
    }

    /**
     * Método responsável por excluir o código.
     * @param string $idUsuario
     * @return bool $resultado
     */
    public function excluir($idUsuario)
    {
        $query = "DELETE FROM tbcodigos_redefinicao_senha WHERE fkUsuId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_LOB);
        return $stmt->execute();
    }

    /**
     * Verifica se o código de redefinição de senha é válido.
     * @param string $idUsuario
     * @param string $codValor
     * @return array|false
     */
    public function validar($idUsuario, $codValor)
    {
        $query = "SELECT codValor FROM tbcodigos_redefinicao_senha WHERE fkUsuId = :usuId AND codUsado = FALSE AND codExpiracao > CURRENT_TIMESTAMP() ORDER BY codExpiracao DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":usuId", $idUsuario, PDO::PARAM_LOB);
        $stmt->execute();
        $codigo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $codigo && password_verify($codValor, $codigo['codValor']);
    }
}
