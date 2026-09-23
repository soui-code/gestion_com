<?php

class Paginator
{
    private $con;
    private $table;
    private $conditions = [];
    private $bindings = [];
    private $perPage = 20;
    private $currentPage = 1;
    private $orderBy = '';
    private $joins = [];
    private $select = '*';

    public function __construct($table)
    {
        $this->con = getDatabaseConnection();
        $this->table = $table;
        $this->currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    }

    public function select($columns)
    {
        $this->select = $columns;
        return $this;
    }

    public function where($condition, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $val) {
                $this->conditions[] = "$key = ?";
                $this->bindings[] = $val;
            }
        } else {
            $this->conditions[] = $condition;
            if ($value !== null) {
                $this->bindings[] = $value;
            }
        }
        return $this;
    }

    public function join($table, $condition, $type = 'LEFT')
    {
        $this->joins[] = "$type JOIN $table ON $condition";
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function perPage($perPage)
    {
        $this->perPage = max(1, (int)$perPage);
        return $this;
    }

    public function paginate()
    {
        $total = $this->getTotal();
        $totalPages = ceil($total / $this->perPage);
        $this->currentPage = min($this->currentPage, max(1, $totalPages));

        $offset = ($this->currentPage - 1) * $this->perPage;

        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }

        $sql .= " LIMIT $offset, {$this->perPage}";

        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute($this->bindings);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'current_page' => $this->currentPage,
                'per_page' => $this->perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $this->perPage, $total),
                'has_previous' => $this->currentPage > 1,
                'has_next' => $this->currentPage < $totalPages,
                'previous_page' => max(1, $this->currentPage - 1),
                'next_page' => min($totalPages, $this->currentPage + 1),
            ];
        } catch (Exception $e) {
            Logger::error('Erreur de pagination', ['error' => $e->getMessage(), 'sql' => $sql]);
            throw $e;
        }
    }

    private function getTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }

        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute($this->bindings);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            Logger::error('Erreur de comptage pour pagination', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public static function renderLinks($paginationData, $baseUrl = '')
    {
        if ($paginationData['total_pages'] <= 1) {
            return '';
        }

        $current = $paginationData['current_page'];
        $total = $paginationData['total_pages'];

        if (empty($baseUrl)) {
            $baseUrl = $_SERVER['REQUEST_URI'];
            $baseUrl = preg_replace('/[?&]page=\d+/', '', $baseUrl);
            $baseUrl .= (strpos($baseUrl, '?') === false) ? '?' : '&';
        }

        $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';

        if ($paginationData['has_previous']) {
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . 'page=' . ($current - 1) . '">Précédent</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Précédent</span></li>';
        }

        $start = max(1, $current - 2);
        $end = min($total, $current + 2);

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . 'page=1">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . 'page=' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($end < $total) {
            if ($end < $total - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . 'page=' . $total . '">' . $total . '</a></li>';
        }

        if ($paginationData['has_next']) {
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . 'page=' . ($current + 1) . '">Suivant</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Suivant</span></li>';
        }

        $html .= '</ul></nav>';

        $html .= '<div class="text-center text-muted mb-3">';
        $html .= 'Affichage de ' . $paginationData['from'] . ' à ' . $paginationData['to'] . ' sur ' . $paginationData['total'] . ' résultats';
        $html .= '</div>';

        return $html;
    }
}
