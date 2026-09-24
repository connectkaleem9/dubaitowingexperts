<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Area;
use App\Models\Faq;
use App\Models\Service;
use App\Services\HtmlSanitizer;
use App\Validation\Validator;

final class FaqController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('faqs/index', [
            'title' => 'FAQs',
            'actions' => '<a class="a-btn a-btn--primary" href="/admin/faqs/new/">Add FAQ</a>',
            'items' => Faq::all(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->form(null);
    }

    public function edit(Request $request): void
    {
        $this->form(Faq::find($this->id($request)) ?? abort(404));
    }

    private function form(?array $faq): void
    {
        $this->view('faqs/form', [
            'title' => $faq ? 'Edit FAQ' : 'Add FAQ',
            'faq' => $faq,
            'services' => array_column(Service::all(), 'name', 'id'),
            'areas' => array_column(Area::all(), 'name', 'id'),
        ]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, '/admin/faqs/new/');
        $id = Faq::create($data);
        $this->log($request, 'faq_created', 'faq', $id, $data['question']);
        $this->success('FAQ added.', '/admin/faqs/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        Faq::find($id) ?? abort(404);
        Faq::update($id, $this->validated($request, '/admin/faqs/' . $id . '/'));
        $this->log($request, 'faq_updated', 'faq', $id);
        $this->success('FAQ saved.', '/admin/faqs/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        Faq::find($id) ?? abort(404);
        Faq::delete($id);
        $this->log($request, 'faq_deleted', 'faq', $id);
        $this->success('FAQ deleted.', '/admin/faqs/');
    }

    private function validated(Request $request, string $back): array
    {
        $v = Validator::make($request->all(), [
            'question' => 'required|max:255',
            'answer' => 'required|max:20000',
            'category' => 'required|max:60',
            'service_id' => 'nullable|int',
            'area_id' => 'nullable|int',
            'sort_order' => 'nullable|int',
        ]);
        $errors = $v->errors();
        $serviceId = $this->optionalId($request, 'service_id');
        $areaId = $this->optionalId($request, 'area_id');
        if ($serviceId !== null && Service::find($serviceId) === null) {
            $errors['service_id'] = 'Choose a valid service.';
        }
        if ($areaId !== null && Area::find($areaId) === null) {
            $errors['area_id'] = 'Choose a valid area.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $request->all(), $back);
        }
        return [
            'question' => $request->input('question'),
            'answer' => HtmlSanitizer::clean($request->input('answer')),
            'category' => $request->input('category'),
            'service_id' => $serviceId,
            'area_id' => $areaId,
            'show_on_home' => $request->input('show_on_home') === '1' ? 1 : 0,
            'sort_order' => (int) $request->input('sort_order'),
            'is_published' => $request->input('is_published') === '1' ? 1 : 0,
        ];
    }
}
