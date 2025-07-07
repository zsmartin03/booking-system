<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\ReviewVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Business $business)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user has already reviewed this business
        if ($business->reviews()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'You have already reviewed this business'], 400);
        }

        // Check if user has had a booking with this business
        $hasBooking = $user->hasBookingWithBusiness($business->id);

        $review = $business->reviews()->create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'has_booking' => $hasBooking,
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('user'),
            'message' => 'Review submitted successfully!'
        ]);
    }

    public function vote(Request $request, Review $review)
    {
        $request->validate([
            'is_upvote' => 'required|boolean',
        ]);

        $user = Auth::user();

        // Check if user has already voted on this review
        $existingVote = $review->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->is_upvote == $request->is_upvote) {
                // Same vote, remove it
                $existingVote->delete();
            } else {
                // Different vote, update it
                $existingVote->update(['is_upvote' => $request->is_upvote]);
            }
        } else {
            // Create new vote
            $review->votes()->create([
                'user_id' => $user->id,
                'is_upvote' => $request->is_upvote,
            ]);
        }

        return response()->json([
            'success' => true,
            'upvotes' => $review->upvotes()->count(),
            'downvotes' => $review->downvotes()->count(),
            'user_vote' => $review->getUserVoteType($user->id),
        ]);
    }

    public function respond(Request $request, Review $review)
    {
        $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user owns the business
        if ($review->business->user_id !== $user->id) {
            return response()->json(['error' => 'You can only respond to reviews of your own business'], 403);
        }

        // Check if there's already a response
        if ($review->response) {
            $review->response->update(['response' => $request->response]);
        } else {
            $review->response()->create([
                'user_id' => $user->id,
                'response' => $request->response,
            ]);
        }

        // Reload the relationship to get the updated response
        $review->load('response.user');

        return response()->json([
            'success' => true,
            'response' => $review->response,
            'message' => 'Response submitted successfully!'
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user owns the review
        if ($review->user_id !== $user->id) {
            return response()->json(['error' => 'You can only edit your own reviews'], 403);
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('user'),
            'message' => 'Review updated successfully!'
        ]);
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();

        // Check if user owns the review
        if ($review->user_id !== $user->id) {
            return response()->json(['error' => 'You can only delete your own reviews'], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully!'
        ]);
    }

    public function updateResponse(Request $request, ReviewResponse $reviewResponse)
    {
        $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user owns the response (business owner)
        if ($reviewResponse->user_id !== $user->id) {
            return response()->json(['error' => 'You can only edit your own responses'], 403);
        }

        $reviewResponse->update([
            'response' => $request->response,
        ]);

        return response()->json([
            'success' => true,
            'response' => $reviewResponse->load('user'),
            'message' => 'Response updated successfully!'
        ]);
    }

    public function destroyResponse(ReviewResponse $reviewResponse)
    {
        $user = Auth::user();

        // Check if user owns the response (business owner)
        if ($reviewResponse->user_id !== $user->id) {
            return response()->json(['error' => 'You can only delete your own responses'], 403);
        }

        $reviewResponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Response deleted successfully!'
        ]);
    }
}
