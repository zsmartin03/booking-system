<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\ReviewResponse;
use App\Models\Booking;
use App\Models\Service;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Get all businesses and users
        $businesses = Business::all();
        $users = User::where('role', 'client')->get();

        // If we don't have enough client users, create more
        if ($users->count() < 15) {
            for ($i = $users->count(); $i < 15; $i++) {
                $users->push(User::create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => bcrypt('password'),
                    'role' => 'client',
                    'email_verified_at' => now(),
                ]));
            }
            $users = User::where('role', 'client')->get();
        }

        // Sample review comments by rating
        $reviewComments = [
            5 => [
                "Absolutely fantastic service! Exceeded all my expectations. The staff was professional and friendly.",
                "Outstanding experience from start to finish. Will definitely come back and recommend to friends.",
                "Perfect! Everything was exactly as promised. Great attention to detail and customer service.",
                "Exceptional quality and service. The team went above and beyond to ensure satisfaction.",
                "Best experience I've had! Professional, efficient, and friendly. Highly recommended!",
                "Amazing work! The quality is top-notch and the staff is incredibly knowledgeable.",
            ],
            4 => [
                "Very good service overall. Minor issues but mostly satisfied with the experience.",
                "Great quality work. A few small things could be improved but generally very happy.",
                "Really pleased with the service. Professional staff and good value for money.",
                "Solid experience. Would recommend with minor reservations. Good customer service.",
                "Pretty good overall. Met most of my expectations with room for small improvements.",
                "Good service and reasonable prices. Would likely return in the future.",
            ],
            3 => [
                "Average experience. Nothing particularly wrong but nothing exceptional either.",
                "Decent service. Met basic expectations but could use some improvements.",
                "Okay experience overall. Some good points and some areas for improvement.",
                "Fair service for the price. Not bad but not outstanding either.",
                "Middle of the road experience. Adequate but not memorable in any way.",
                "Standard service. Does the job but doesn't go the extra mile.",
            ],
            2 => [
                "Below expectations. Several issues that affected the overall experience.",
                "Disappointing service. Had higher hopes based on the description.",
                "Not great. Multiple small problems that added up to a poor experience.",
                "Subpar experience. Would hesitate to recommend or return.",
                "Had some problems during the service. Not entirely satisfied.",
                "Expected better quality for the price paid. Several issues encountered.",
            ],
            1 => [
                "Very disappointing experience. Multiple significant issues throughout.",
                "Poor service quality. Would not recommend to others based on this experience.",
                "Unacceptable service level. Many problems that weren't addressed properly.",
                "Terrible experience. Nothing went as expected and staff was unhelpful.",
                "Completely unsatisfied. Major issues that ruined the entire experience.",
                "Worst service I've experienced. Would definitely not return or recommend.",
            ],
        ];

        foreach ($businesses as $business) {
            // Create 8-15 reviews per business
            $reviewCount = rand(8, 15);
            $usedUsers = [];

            // Get users who haven't already reviewed this business
            $availableUsers = $users->filter(function ($user) use ($business) {
                return !Review::where('business_id', $business->id)
                    ->where('user_id', $user->id)
                    ->exists();
            });

            // Limit review count to available users
            $reviewCount = min($reviewCount, $availableUsers->count());

            for ($i = 0; $i < $reviewCount; $i++) {
                // Get a random user that hasn't reviewed this business yet
                do {
                    $user = $availableUsers->random();
                    $availableUsers = $availableUsers->reject(function ($u) use ($user) {
                        return $u->id === $user->id;
                    });
                } while (in_array($user->id, $usedUsers) && $availableUsers->count() > 0);

                if (in_array($user->id, $usedUsers)) {
                    break; // No more unique users available
                }

                $usedUsers[] = $user->id;

                // Generate a rating with bias towards higher ratings (more realistic)
                $rating = $this->generateRealisticRating();

                // Create some bookings for verified reviews (about 60% will have bookings)
                $hasBooking = rand(1, 100) <= 60;
                if ($hasBooking && $business->services->count() > 0) {
                    $service = $business->services->random();
                    Booking::create([
                        'client_id' => $user->id,
                        'service_id' => $service->id,
                        'employee_id' => $business->employees->count() > 0 ? $business->employees->random()->id : null,
                        'start_time' => $faker->dateTimeBetween('-3 months', '-1 week'),
                        'end_time' => $faker->dateTimeBetween('-3 months', '-1 week'),
                        'status' => 'completed',
                        'total_price' => $service->price,
                        'notes' => null,
                    ]);
                }

                // Create the review
                $review = Review::create([
                    'business_id' => $business->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $faker->randomElement($reviewComments[$rating]),
                    'has_booking' => $hasBooking,
                    'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                ]);

                // Add some votes to reviews (about 40% of reviews get votes)
                if (rand(1, 100) <= 40) {
                    $voteCount = rand(1, 5);
                    $votedUsers = [];

                    for ($v = 0; $v < $voteCount; $v++) {
                        do {
                            $voter = $users->random();
                        } while (in_array($voter->id, $votedUsers) || $voter->id === $user->id);

                        $votedUsers[] = $voter->id;

                        ReviewVote::create([
                            'review_id' => $review->id,
                            'user_id' => $voter->id,
                            'is_upvote' => rand(1, 100) <= 80, // 80% upvotes, 20% downvotes
                        ]);
                    }
                }

                // Business owners respond to some reviews (about 30% get responses)
                if (rand(1, 100) <= 30) {
                    $responses = [
                        5 => [
                            "Thank you so much for your wonderful review! We're thrilled you had such a great experience.",
                            "We really appreciate your kind words! It was our pleasure to serve you.",
                            "Thank you for choosing us and for taking the time to share your experience!",
                        ],
                        4 => [
                            "Thank you for your positive feedback! We're glad you enjoyed our service.",
                            "We appreciate your review and are happy you had a good experience with us.",
                            "Thanks for the great review! We're always working to improve our service.",
                        ],
                        3 => [
                            "Thank you for your honest feedback. We'll take your comments into consideration.",
                            "We appreciate your review and are always looking for ways to improve.",
                            "Thanks for taking the time to review us. We value your input.",
                        ],
                        2 => [
                            "Thank you for your feedback. We're sorry we didn't meet your expectations and will work to improve.",
                            "We appreciate your honest review and apologize for any inconvenience.",
                            "Thank you for bringing these issues to our attention. We'll address them immediately.",
                        ],
                        1 => [
                            "We sincerely apologize for your poor experience. Please contact us directly so we can make this right.",
                            "We're very sorry to hear about your experience. We'd like to discuss this with you personally.",
                            "This is not the level of service we strive for. Please reach out so we can resolve these issues.",
                        ],
                    ];

                    ReviewResponse::create([
                        'review_id' => $review->id,
                        'user_id' => $business->user_id,
                        'response' => $faker->randomElement($responses[$rating]),
                        'created_at' => $faker->dateTimeBetween($review->created_at, 'now'),
                    ]);
                }
            }
        }
    }

    /**
     * Generate a realistic rating distribution
     * Most businesses have more 4-5 star reviews than low ratings
     */
    private function generateRealisticRating(): int
    {
        $rand = rand(1, 100);

        if ($rand <= 35) return 5;      // 35% - 5 stars
        if ($rand <= 60) return 4;      // 25% - 4 stars
        if ($rand <= 75) return 3;      // 15% - 3 stars
        if ($rand <= 90) return 2;      // 15% - 2 stars
        return 1;                       // 10% - 1 star
    }
}
