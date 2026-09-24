<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Session;
use App\Models\Review;
use App\Models\Service;
use App\Services\FormGuard;
use App\Services\Seo;
use App\Services\Uploader;
use App\Validation\Validator;

final class ReviewController extends Controller
{
    public function index(Request $request): void
    {
        $perPage = (int) config('app.per_page');
        $page = $request->queryInt('page', 1);
        $result = Review::paginateApproved($page, $perPage);
        $pager = $this->pagination($page, $result['total'], $perPage);

        $seo = Seo::page(
            '/reviews/',
            'Customer Reviews | ' . business('name'),
            'Read reviews from drivers we have helped across Dubai, or leave your own review of our car recovery and towing service.',
            $result['total'] === 0 ? 'noindex,follow' : 'index,follow'
        )->type('reviews')->crumbs('Reviews', '/reviews/');
        if ($page > 1) {
            $seo->path = '/reviews/?page=' . $page;
            $seo->title = 'Page ' . $page . ' – ' . $seo->title;
        }

        $this->view('reviews/index', [
            'seo' => $seo,
            'reviews' => $result['items'],
            'reviewImages' => Review::imagesFor(array_column($result['items'], 'id')),
            'stats' => Review::approvedStats(),
            'services' => Service::published(),
            'pager' => $pager,
            'submitted' => (bool) Session::getFlash('review_submitted', false),
        ]);
    }

    public function store(Request $request): void
    {
        $guard = FormGuard::check($request, 'review', 3, 3600);
        if ($guard === FormGuard::SPAM) {
            Session::flash('review_submitted', true);
            redirect('/reviews/#review-form');
        }

        $data = $request->all();
        $v = Validator::make($data, [
            'name' => 'required|max:100|no_links',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|phone',
            'service_id' => 'nullable|int',
            'area_text' => 'nullable|max:120|no_links',
            'rating' => 'required|int|min_value:1|max_value:5',
            'body' => 'required|min:20|max:2000',
            'consent' => 'accepted',
        ], ['body' => 'Your review', 'area_text' => 'Area', 'consent' => 'that we may publish your review']);

        $errors = $v->errors();
        if ($guard !== null) {
            $errors['_form'] = $guard;
        }
        $serviceId = (int) $request->input('service_id');
        if ($serviceId > 0 && Service::find($serviceId) === null) {
            $errors['service_id'] = 'Choose a valid service.';
        }
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', array_diff_key($data, ['_token' => 1, '_ts' => 1]));
            redirect('/reviews/#review-form');
        }

        $reviewId = Review::create([
            'name' => $request->input('name'),
            'email' => $request->input('email') ?: null,
            'phone' => $request->input('phone') ?: null,
            'service_id' => $serviceId > 0 ? $serviceId : null,
            'area_text' => $request->input('area_text') ?: null,
            'rating' => (int) $request->input('rating'),
            'body' => $request->input('body'),
            'status' => 'pending',
            'consent_at' => date('Y-m-d H:i:s'),
            'ip_hash' => ip_hash($request->ip()),
        ]);

        $photo = $request->file('photo');
        if ($photo !== null) {
            try {
                $mediaId = Uploader::image($photo, 'Customer photo from review by ' . $request->input('name'), null, 5 * 1024 * 1024);
                Review::attachImage($reviewId, $mediaId);
            } catch (\RuntimeException $e) {
                // Keep the review; the photo is optional. Tell the customer.
                Session::flash('photo_error', $e->getMessage());
            } catch (\Throwable $e) {
                ErrorHandler::report($e);
            }
        }

        Session::flash('review_submitted', true);
        redirect('/reviews/#review-form');
    }
}
