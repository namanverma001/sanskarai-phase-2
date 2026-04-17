<?php
/**
 * Sanskar AI - Feedback Controller
 * =================================
 * Handles User Feedback requests
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Router;
use App\Models\UserFeedback;

class FeedbackController extends Controller
{
    private UserFeedback $feedbackModel;

    public function __construct()
    {
        parent::__construct();
        // Require auth
        if (!Auth::check()) {
            Router::redirect('/login');
            exit;
        }
        $this->feedbackModel = new UserFeedback();
    }

    /**
     * Show Feedback Form or Existing Feedback
     */
    public function index(): void
    {
        $userId = Auth::id();
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);
        
        $isLogoutFlow = isset($_GET['logout']) && $_GET['logout'] == '1';
        $forceNew = isset($_GET['new']) && $_GET['new'] == '1';

        $existingFeedback = null;
        if (!$forceNew) {
            // Fetch the most recent feedback from this user
            $feedbacks = $this->feedbackModel->where(['user_id' => $userId], 'created_at', 'DESC');
            $existingFeedback = $feedbacks[0] ?? null;
        }
        
        $viewData = [
            'title' => 'Feedback - Sanskar AI',
            'user' => $user,
            'existingFeedback' => $existingFeedback,
            'isLogoutFlow' => $isLogoutFlow,
            'forceNew' => $forceNew
        ];

        $this->viewWithLayout('user/feedback', 'layouts/user', $viewData);
    }

    /**
     * Handle Feedback Form Submission
     */
    public function submit(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $userId = Auth::id();
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);
        
        $featuresFeedback = $this->input('features_feedback', []); // will be an array
        $likesAbout = $this->input('likes_about', '');
        $improvementsFor = $this->input('improvements_for', '');
        $isLogoutFlow = $this->input('is_logout_flow') == '1';

        // Additional base info from user object, or input if they can overwrite
        $name = $this->input('name', $user['name'] ?? '');
        $email = $this->input('email', $user['email'] ?? '');
        $phone = $this->input('phone', $user['mobile'] ?? '');
        $community = $this->input('community_name', $user['community_name'] ?? '');

        // Validation
        if (empty($likesAbout)) {
            $this->back(['error' => 'Please let us know what you like about Sanskar AI.']);
            return;
        }

        // Prepare and cleanup features feedback
        $processedFeatures = [];
        if (is_array($featuresFeedback)) {
            foreach ($featuresFeedback as $featureName => $feedback) {
                // Under the new UI, the feature name is nested under ['name']
                $featureName = $feedback['name'] ?? null;
                
                // If they didn't select a feature in the dropdown, skip it
                if (empty($featureName)) {
                    continue;
                }
                
                $comment = $feedback['comment'] ?? '';
                $rating = $feedback['rating'] ?? '';
                
                if ($rating && $comment) {
                    $processedFeatures[$featureName] = "[$rating/5 Stars] - $comment";
                } elseif ($rating) {
                    $processedFeatures[$featureName] = "[$rating/5 Stars]";
                } elseif ($comment) {
                    $processedFeatures[$featureName] = $comment;
                } else {
                    $processedFeatures[$featureName] = "No additional comment provided.";
                }
            }
        }

        $data = [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'community_name' => $community,
            'features_feedback' => $processedFeatures, // Will be JSON encoded in model
            'likes_about' => $likesAbout,
            'improvements_for' => $improvementsFor
        ];

        $this->feedbackModel->storeFeedback($data);

        if ($isLogoutFlow) {
            Auth::logout();
            $this->redirect('/login', ['success' => 'Thank you for your feedback! You have been logged out successfully.']);
        } else {
            $this->redirect('/user/feedback', ['success' => 'Thank you for your feedback!']);
        }
    }
}
