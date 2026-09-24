<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Lead;
use App\Validation\Validator;

final class LeadController extends AdminController
{
    public function index(Request $request): void
    {
        $page = $this->page($request);
        $status = $request->query('status');
        $q = $request->query('q');
        $result = Lead::adminList($page, 25, $status, $q);
        $this->view('leads/index', [
            'title' => 'Contact Requests',
            'actions' => '<a class="a-btn" href="/admin/leads/export/">Export CSV</a>',
            'items' => $result['items'],
            'pager' => ['page' => $page, 'pages' => max(1, (int) ceil($result['total'] / 25))],
            'status' => $status,
            'q' => $q,
            'counts' => Lead::countByStatus(),
        ]);
    }

    public function show(Request $request): void
    {
        $lead = Lead::find($this->id($request)) ?? abort(404);
        $this->view('leads/show', ['title' => 'Enquiry #' . (int) $lead['id'], 'lead' => $lead]);
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        Lead::find($id) ?? abort(404);
        $v = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'admin_notes' => 'nullable|max:5000',
        ]);
        if ($v->fails()) {
            $this->invalid($v->errors(), $request->all(), '/admin/leads/' . $id . '/');
        }
        Lead::update($id, ['status' => $request->input('status'), 'admin_notes' => $request->input('admin_notes') ?: null]);
        $this->log($request, 'lead_updated', 'lead', $id, 'status=' . $request->input('status'));
        $this->success('Enquiry updated.', '/admin/leads/' . $id . '/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        Lead::find($id) ?? abort(404);
        Lead::delete($id);
        $this->log($request, 'lead_deleted', 'lead', $id);
        $this->success('Enquiry deleted.', '/admin/leads/');
    }

    public function export(Request $request): void
    {
        $rows = Lead::adminList(1, 5000, $request->query('status'), $request->query('q'))['items'];
        $this->log($request, 'leads_exported', 'lead', null, count($rows) . ' rows');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads UTF-8
        fputcsv($out, ['ID', 'Received', 'Name', 'Phone', 'Email', 'Service', 'Location', 'Vehicle', 'Preferred contact', 'Message', 'Status', 'Source page', 'UTM source', 'UTM campaign', 'GCLID']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'], $r['created_at'], $r['name'],
                // Leading apostrophe stops spreadsheets treating +971… or =… as a formula.
                "'" . $r['phone'], $r['email'], $r['service_name'] ?? $r['service_text'], $r['location'], $r['vehicle_type'],
                $r['preferred_contact'], $r['message'], $r['status'], $r['source_path'], $r['utm_source'], $r['utm_campaign'], $r['gclid'],
            ]);
        }
        fclose($out);
    }
}
