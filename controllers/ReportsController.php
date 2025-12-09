<?php
require_once __DIR__ . '/../models/BaseModel.php';

class ReportsController {
    private $pdo;
    public function __construct() {
        $this->pdo = (new BaseModel())->getConnection();
    }

    public function profit() {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $paramsRev = [];
        $paramsCost = [];

        $sqlRev = "SELECT t.id AS tour_id, t.title AS tour_title, COALESCE(SUM(p.amount),0) AS revenue,
                           COUNT(DISTINCT b.id) AS booking_count
                    FROM tours t
                    LEFT JOIN bookings b ON b.tour_id = t.id
                    LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'completed'";
        if ($start && $end) { $sqlRev .= " AND p.payment_date BETWEEN ? AND ?"; $paramsRev[] = $start; $paramsRev[] = $end; }
        $sqlRev .= " GROUP BY t.id, t.title ORDER BY revenue DESC";
        $stmtRev = $this->pdo->prepare($sqlRev);
        $stmtRev->execute($paramsRev);
        $revenueRows = $stmtRev->fetchAll(PDO::FETCH_ASSOC);

        $sqlCost = "SELECT te.tour_id, COALESCE(SUM(te.amount),0) AS cost
                    FROM tour_expenses te";
        if ($start && $end) { $sqlCost .= " WHERE te.date_incurred BETWEEN ? AND ?"; $paramsCost[] = $start; $paramsCost[] = $end; }
        $sqlCost .= " GROUP BY te.tour_id";
        $stmtCost = $this->pdo->prepare($sqlCost);
        $stmtCost->execute($paramsCost);
        $costRows = $stmtCost->fetchAll(PDO::FETCH_ASSOC);

        $costByTour = [];
        foreach ($costRows as $r) { $costByTour[(int)$r['tour_id']] = (float)$r['cost']; }

        $rows = [];
        foreach ($revenueRows as $r) {
            $tid = (int)$r['tour_id'];
            $rev = (float)($r['revenue'] ?? 0);
            $cost = (float)($costByTour[$tid] ?? 0);
            $profit = $rev - $cost;
            $margin = $rev > 0 ? round(($profit / $rev) * 100, 2) : 0;
            $rows[] = [
                'tour_id' => $tid,
                'tour_title' => $r['tour_title'] ?? '',
                'revenue' => $rev,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => $margin,
                'booking_count' => (int)($r['booking_count'] ?? 0)
            ];
        }

        $trendStart = $start ?: date('Y-m-01', strtotime('-5 months'));
        $trendEnd = $end ?: date('Y-m-t');
        $stmtTRev = $this->pdo->prepare("SELECT DATE_FORMAT(p.payment_date,'%Y-%m') m, COALESCE(SUM(p.amount),0) s FROM payments p WHERE p.status='completed' AND p.payment_date BETWEEN ? AND ? GROUP BY m ORDER BY m");
        $stmtTRev->execute([$trendStart, $trendEnd]);
        $revTrendRows = $stmtTRev->fetchAll(PDO::FETCH_ASSOC);
        $stmtTCost = $this->pdo->prepare("SELECT DATE_FORMAT(te.date_incurred,'%Y-%m') m, COALESCE(SUM(te.amount),0) s FROM tour_expenses te WHERE te.date_incurred BETWEEN ? AND ? GROUP BY m ORDER BY m");
        $stmtTCost->execute([$trendStart, $trendEnd]);
        $costTrendRows = $stmtTCost->fetchAll(PDO::FETCH_ASSOC);
        $mapRev = []; $mapCost = []; $months = [];
        foreach ($revTrendRows as $r) { $m=$r['m']; $months[$m]=true; $mapRev[$m]=(float)$r['s']; }
        foreach ($costTrendRows as $r) { $m=$r['m']; $months[$m]=true; $mapCost[$m]=(float)$r['s']; }
        ksort($months);
        $trendLabels = array_keys($months);
        $trendRevenue = []; $trendCost = []; $trendProfit = [];
        foreach ($trendLabels as $m) { $rv=$mapRev[$m]??0; $ct=$mapCost[$m]??0; $trendRevenue[]=$rv; $trendCost[]=$ct; $trendProfit[]=$rv-$ct; }
        require __DIR__ . '/../views/reports/profit.php';
    }

    public function profitDetail() {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        if ($tourId <= 0) { echo 'Invalid tour'; return; }
        $stmtB = $this->pdo->prepare("SELECT b.id, b.date_booked, b.total_price, b.booking_status,
                                             COALESCE(SUM(CASE WHEN p.status='completed' THEN p.amount END),0) AS paid
                                      FROM bookings b
                                      LEFT JOIN payments p ON p.booking_id = b.id" . ($start&&$end?" AND p.payment_date BETWEEN ? AND ?":"") . "
                                      WHERE b.tour_id = ?
                                      GROUP BY b.id, b.date_booked, b.total_price, b.booking_status
                                      ORDER BY b.id DESC");
        $paramsB = []; if ($start&&$end){ $paramsB[]=$start; $paramsB[]=$end; } $paramsB[]=$tourId; $stmtB->execute($paramsB);
        $bookings = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        $stmtE = $this->pdo->prepare("SELECT te.id, te.expense_type, te.description, te.amount, te.date_incurred, s.name AS supplier_name
                                      FROM tour_expenses te
                                      LEFT JOIN suppliers s ON te.supplier_id = s.id
                                      WHERE te.tour_id = ?" . ($start&&$end?" AND te.date_incurred BETWEEN ? AND ?":"") . "
                                      ORDER BY te.date_incurred DESC, te.id DESC");
        $paramsE = [$tourId]; if ($start&&$end){ $paramsE[]=$start; $paramsE[]=$end; } $stmtE->execute($paramsE);
        $expenses = $stmtE->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/reports/profit_detail.php';
    }

    public function export() {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $format = strtolower($_GET['format'] ?? 'csv');
        $paramsRev = []; $paramsCost = [];
        $sqlRev = "SELECT t.id AS tour_id, t.title AS tour_title, COALESCE(SUM(p.amount),0) AS revenue,
                           COUNT(DISTINCT b.id) AS booking_count
                    FROM tours t
                    LEFT JOIN bookings b ON b.tour_id = t.id
                    LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'completed'";
        if ($start && $end) { $sqlRev .= " AND p.payment_date BETWEEN ? AND ?"; $paramsRev[] = $start; $paramsRev[] = $end; }
        $sqlRev .= " GROUP BY t.id, t.title ORDER BY revenue DESC";
        $stmtRev = $this->pdo->prepare($sqlRev);
        $stmtRev->execute($paramsRev);
        $revenueRows = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
        $sqlCost = "SELECT te.tour_id, COALESCE(SUM(te.amount),0) AS cost FROM tour_expenses te";
        if ($start && $end) { $sqlCost .= " WHERE te.date_incurred BETWEEN ? AND ?"; $paramsCost[] = $start; $paramsCost[] = $end; }
        $sqlCost .= " GROUP BY te.tour_id";
        $stmtCost = $this->pdo->prepare($sqlCost);
        $stmtCost->execute($paramsCost);
        $costByTour = [];
        foreach ($stmtCost->fetchAll(PDO::FETCH_ASSOC) as $r) { $costByTour[(int)$r['tour_id']] = (float)$r['cost']; }
        $rows = [];
        foreach ($revenueRows as $r) {
            $tid = (int)$r['tour_id'];
            $rev = (float)($r['revenue'] ?? 0);
            $cost = (float)($costByTour[$tid] ?? 0);
            $profit = $rev - $cost;
            $margin = $rev > 0 ? round(($profit / $rev) * 100, 2) : 0;
            $rows[] = [
                'tour_title' => $r['tour_title'] ?? '',
                'revenue' => $rev,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => $margin,
                'booking_count' => (int)($r['booking_count'] ?? 0)
            ];
        }
        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=reports_profit_' . date('Ymd_His') . '.csv');
            $out = fopen('php://output','w');
            fputcsv($out, ['Tour','Doanh thu','Chi phí','Lợi nhuận','Biên lợi nhuận','Số booking']);
            foreach ($rows as $r) { fputcsv($out, [$r['tour_title'], $r['revenue'], $r['cost'], $r['profit'], $r['margin'], $r['booking_count']]); }
            fclose($out);
            exit;
        }
        require __DIR__ . '/../views/reports/profit_print.php';
    }

    public function debts() {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $status = $_GET['status'] ?? '';
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $minRemaining = isset($_GET['min_remaining']) ? (float)$_GET['min_remaining'] : 0;

        $conds = [];
        $params = [];
        if ($tourId > 0) { $conds[] = 'b.tour_id = ?'; $params[] = $tourId; }
        if ($status !== '') { $conds[] = 'b.booking_status = ?'; $params[] = $status; }
        $dateCond = '';
        if ($start !== '' && $end !== '') { $dateCond = ' AND p.payment_date BETWEEN ? AND ?'; $params[] = $start; $params[] = $end; }
        $where = $conds ? ('WHERE ' . implode(' AND ', $conds)) : '';

        $sql = "SELECT b.id, t.title AS tour_title, b.total_price,
                        COALESCE(SUM(CASE WHEN p.status='completed' THEN p.amount END),0) AS paid,
                        b.booking_status, b.date_booked
                 FROM bookings b
                 LEFT JOIN tours t ON b.tour_id = t.id
                 LEFT JOIN payments p ON p.booking_id = b.id" . $dateCond . "
                 $where
                 GROUP BY b.id, t.title, b.total_price, b.booking_status, b.date_booked
                 ORDER BY b.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        foreach ($rows as $r) {
            $remaining = (float)($r['total_price'] ?? 0) - (float)($r['paid'] ?? 0);
            if ($remaining < $minRemaining) continue;
            $items[] = [
                'booking_id' => (int)$r['id'],
                'tour_title' => $r['tour_title'] ?? '',
                'total' => (float)($r['total_price'] ?? 0),
                'paid' => (float)($r['paid'] ?? 0),
                'remaining' => $remaining,
                'status' => $r['booking_status'] ?? '',
                'date_booked' => $r['date_booked'] ?? null,
            ];
        }
        require __DIR__ . '/../views/reports/debts.php';
    }

    public function debtsExport() {
        $tourId = (int)($_GET['tour_id'] ?? 0);
        $status = $_GET['status'] ?? '';
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $minRemaining = isset($_GET['min_remaining']) ? (float)$_GET['min_remaining'] : 0;

        $conds = [];
        $params = [];
        if ($tourId > 0) { $conds[] = 'b.tour_id = ?'; $params[] = $tourId; }
        if ($status !== '') { $conds[] = 'b.booking_status = ?'; $params[] = $status; }
        $dateCond = '';
        if ($start !== '' && $end !== '') { $dateCond = ' AND p.payment_date BETWEEN ? AND ?'; $params[] = $start; $params[] = $end; }
        $where = $conds ? ('WHERE ' . implode(' AND ', $conds)) : '';

        $sql = "SELECT b.id, t.title AS tour_title, b.total_price,
                        COALESCE(SUM(CASE WHEN p.status='completed' THEN p.amount END),0) AS paid,
                        b.booking_status, b.date_booked
                 FROM bookings b
                 LEFT JOIN tours t ON b.tour_id = t.id
                 LEFT JOIN payments p ON p.booking_id = b.id" . $dateCond . "
                 $where
                 GROUP BY b.id, t.title, b.total_price, b.booking_status, b.date_booked
                 ORDER BY b.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        foreach ($rows as $r) {
            $remaining = (float)($r['total_price'] ?? 0) - (float)($r['paid'] ?? 0);
            if ($remaining < $minRemaining) continue;
            $items[] = [
                'booking_id' => (int)$r['id'],
                'tour_title' => $r['tour_title'] ?? '',
                'total' => (float)($r['total_price'] ?? 0),
                'paid' => (float)($r['paid'] ?? 0),
                'remaining' => $remaining,
                'status' => $r['booking_status'] ?? '',
                'date_booked' => $r['date_booked'] ?? null,
            ];
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reports_debts_' . date('Ymd_His') . '.csv');
        $out = fopen('php://output','w');
        fputcsv($out, ['Booking','Tour','Tổng tiền','Đã thu','Còn nợ','Trạng thái','Ngày đặt']);
        foreach ($items as $it) { fputcsv($out, [$it['booking_id'], $it['tour_title'], $it['total'], $it['paid'], $it['remaining'], $it['status'], $it['date_booked']]); }
        fclose($out);
        exit;
    }
}
?>
