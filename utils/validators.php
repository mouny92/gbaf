<?php

    // firstname
    function is_firstname_valid($firstname) {
        return preg_match('/^[a-zA-Z]{2,16}[ -]{0,1}[a-zA-Z]{1,16}[a-zA-Z]{1}$/', $firstname);
    }

    function firstname_invalid_message() {
        return "Le prénom doit faire entre 4 et 34 caractères et être composé de lettres, d'espaces ou de tirets";
    }

    // lastname
    function is_lastname_valid($lastname) {
        return preg_match('/^[a-zA-Z]{2,16}[ -]{0,1}[a-zA-Z]{1,16}[a-zA-Z]{1}$/', $lastname);
    }

    function lastname_invalid_message() {
        return "Le nom doit faire entre 4 et 34 caractères et être composé de lettres, d'espaces ou de tirets";
    }

    // username
    function is_username_valid($username) {
        return preg_match('/^[a-zA-Z0-9]{4,16}$/', $username);
    }

    function username_invalid_message() {
        return "Le nom d'utilisateur doit faire entre 4 et 16 caractères et être composé de lettres ou de chiffres";
    }

    // password
    function is_password_valid($password) {
        return preg_match('/^[\s\S]{6,64}$/', $password);
    }

    function password_invalid_message() {
        return "Le mot de passe doit faire entre 6 et 64 caractères";
    }

    function is_password_same($password1, $password2) {
        return $password1 == $password2;
    }

    function password_mismatch_message() {
        return "Les mots de passe sont différent";
    }

    // secret_question
    function is_secret_question_valid($secret_question) {
        return preg_match('/^[\s\S]{4,256}$/', $secret_question);
    }

    function secret_question_invalid_message() {
        return "La question secrète doit faire entre 4 et 256 caractères";
    }

    // secret_answer
    function is_secret_answer_valid($secret_answer) {
        return preg_match('/^[\s\S]{4,256}$/', $secret_answer);
    }

    function secret_answer_invalid_message() {
        return "La réponse secrète doit faire entre 4 et 256 caractères";
    }

?>
