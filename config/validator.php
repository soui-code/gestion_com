<?php

class Validator
{
    private $data = [];
    private $errors = [];
    private $rules = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(array $rules)
    {
        $this->rules = $rules;

        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function applyRule($field, $value, $rule)
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $paramString) = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        $method = 'validate' . ucfirst($rule);
        if (method_exists($this, $method)) {
            $this->$method($field, $value, $params);
        }
    }

    private function validateRequired($field, $value, $params)
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " est requis.";
        }
    }

    private function validateEmail($field, $value, $params)
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit être une adresse email valide.";
        }
    }

    private function validateMin($field, $value, $params)
    {
        $min = (int)$params[0];
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit contenir au moins $min caractères.";
        }
    }

    private function validateMax($field, $value, $params)
    {
        $max = (int)$params[0];
        if (!empty($value) && strlen($value) > $max) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " ne doit pas dépasser $max caractères.";
        }
    }

    private function validateNumeric($field, $value, $params)
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit être un nombre.";
        }
    }

    private function validateInteger($field, $value, $params)
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit être un entier.";
        }
    }

    private function validateMinValue($field, $value, $params)
    {
        $min = (float)$params[0];
        if (!empty($value) && (float)$value < $min) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit être supérieur ou égal à $min.";
        }
    }

    private function validateMaxValue($field, $value, $params)
    {
        $max = (float)$params[0];
        if (!empty($value) && (float)$value > $max) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " doit être inférieur ou égal à $max.";
        }
    }

    private function validateIn($field, $value, $params)
    {
        if (!empty($value) && !in_array($value, $params)) {
            $this->errors[$field][] = "Le champ " . $this->getFieldLabel($field) . " contient une valeur invalide.";
        }
    }

    private function validateUnique($field, $value, $params)
    {
        if (empty($value) || count($params) < 2) {
            return;
        }

        $table = $params[0];
        $column = $params[1];
        $exceptId = $params[2] ?? null;
        $exceptColumn = $params[3] ?? 'id';

        try {
            $con = getDatabaseConnection();
            $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
            $bindParams = [$value];

            if ($exceptId !== null) {
                $sql .= " AND $exceptColumn != ?";
                $bindParams[] = $exceptId;
            }

            $stmt = $con->prepare($sql);
            $stmt->execute($bindParams);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                $this->errors[$field][] = "Cette valeur pour " . $this->getFieldLabel($field) . " existe déjà.";
            }
        } catch (Exception $e) {
            Logger::error('Erreur de validation unique', ['error' => $e->getMessage()]);
        }
    }

    private function getFieldLabel($field)
    {
        $labels = [
            'nom' => 'nom',
            'email' => 'email',
            'login' => 'identifiant',
            'mdp' => 'mot de passe',
            'designation' => 'désignation',
            'prix_achat' => 'prix d\'achat',
            'prix_vente' => 'prix de vente',
            'stock' => 'stock',
        ];

        return $labels[$field] ?? str_replace('_', ' ', $field);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function firstError($field = null)
    {
        if ($field !== null) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }

        return null;
    }

    public function allErrors()
    {
        $all = [];
        foreach ($this->errors as $fieldErrors) {
            $all = array_merge($all, $fieldErrors);
        }
        return $all;
    }
}
