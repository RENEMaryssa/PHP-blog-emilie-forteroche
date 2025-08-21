<?php

/**
 * Entité User : un user est défini par son id, un login et un password.
 */
class User extends AbstractEntity
{
    private string $login;
    private string $password;
    private string $pseudo;  // Ajouté
    private string $email;   // Ajouté


    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->pseudo = $data['pseudo'] ?? '';
        $this->email = $data['email'] ?? '';
    }

    /**
     * Setter pour le login.
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * Getter pour le login.
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    // Pseudo
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    // Email
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Setter pour le password.
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Getter pour le password.
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
