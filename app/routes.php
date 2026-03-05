<?php
/**
 * Sanskar AI - Application Routes
 * =================================
 * Define all application routes
 */

use App\Core\Router;

// ============================================================
// PUBLIC ROUTES (Guest)
// ============================================================
Router::group(['middleware' => ['guest']], function () {
    // Authentication
    Router::get('/login', 'AuthController@showLogin', [], 'login');
    Router::post('/login', 'AuthController@login');
    Router::get('/signup', 'AuthController@showSignup', [], 'signup');
    Router::post('/signup', 'AuthController@signup');
    Router::get('/forgot-password', 'AuthController@showForgotPassword');
    Router::post('/forgot-password', 'AuthController@forgotPassword');
    Router::get('/reset-password', 'AuthController@showResetPassword');
    Router::post('/reset-password', 'AuthController@resetPassword');
});

// Logout (requires auth)
Router::post('/logout', 'AuthController@logout', ['auth'], 'logout');

// Home page (Landing page for guests, dashboard redirect for logged in)
Router::get('/', 'HomeController@index', [], 'home');

// ============================================================
// ADMIN ROUTES
// ============================================================
Router::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
    // Dashboard
    Router::get('/dashboard', 'AdminController@dashboard', [], 'admin.dashboard');

    // Admin Profile
    Router::get('/profile', 'AdminController@profile', [], 'admin.profile');
    Router::post('/profile/password', 'AdminController@updatePassword');

    // User Management
    Router::get('/users', 'AdminController@users', [], 'admin.users');
    Router::post('/users/{id}/block', 'AdminController@blockUser');
    Router::post('/users/{id}/activate', 'AdminController@activateUser');
    Router::post('/users/{id}/delete', 'AdminController@deleteUser');

    // Pandit Approval
    Router::get('/pandits/pending', 'AdminController@pendingPandits', [], 'admin.pending-pandits');
    Router::post('/pandits/{id}/approve', 'AdminController@approvePandit');
    Router::post('/pandits/{id}/reject', 'AdminController@rejectPandit');

    // Ritual Management
    Router::get('/rituals', 'AdminController@rituals', [], 'admin.rituals');
    Router::get('/rituals/create', 'AdminController@createRitual');
    
    // AI Ritual Generation (Admin Only - saves to global rituals)
    // IMPORTANT: These must be BEFORE wildcard routes like /rituals/{id}
    Router::get('/rituals/generate', 'AdminController@generateRitualForm');
    Router::post('/rituals/generate', 'AdminController@generateRitual');
    
    Router::post('/rituals', 'AdminController@storeRitual');
    Router::get('/rituals/{id}/edit', 'AdminController@editRitual');
    Router::post('/rituals/{id}', 'AdminController@updateRitual');
    Router::post('/rituals/{id}/delete', 'AdminController@deleteRitual');

    // Ritual Steps Management
    Router::post('/rituals/{id}/steps', 'AdminController@addRitualStep');
    Router::post('/ritual-steps/{id}/update', 'AdminController@updateRitualStep');
    Router::post('/ritual-steps/{id}/delete', 'AdminController@deleteRitualStep');

    // Ritual Items Management
    Router::post('/rituals/{id}/items', 'AdminController@addRitualItem');
    Router::post('/ritual-items/{id}/update', 'AdminController@updateRitualItem');
    Router::post('/ritual-items/{id}/delete', 'AdminController@deleteRitualItem');

    // Pandit Assignment
    Router::get('/assign-pandit', 'AdminController@assignPandit');

    // AI Logs
    Router::get('/ai-logs', 'AdminController@aiLogs', [], 'admin.ai-logs');
    Router::post('/ai-logs/{id}/flag', 'AdminController@flagAIRequest');
    Router::post('/ai-logs/{id}/unflag', 'AdminController@unflagAIRequest');

    // Admin Account Creation
    Router::get('/create-admin', 'AdminController@showCreateAdmin', [], 'admin.create-admin');
    Router::post('/create-admin', 'AdminController@storeAdmin');

    // Vendor Management
    Router::get('/vendors', 'AdminController@vendors', [], 'admin.vendors');
    Router::get('/vendors/create', 'AdminController@createVendor');
    Router::post('/vendors', 'AdminController@storeVendor');
    Router::get('/vendors/{id}/edit', 'AdminController@editVendor');
    Router::post('/vendors/{id}', 'AdminController@updateVendor');
    Router::post('/vendors/{id}/delete', 'AdminController@deleteVendor');
    Router::post('/vendors/{id}/toggle-status', 'AdminController@toggleVendorStatus');
    Router::post('/vendors/{id}/toggle-featured', 'AdminController@toggleVendorFeatured');

    // Review Management
    Router::get('/reviews', 'AdminController@reviews', [], 'admin.reviews');
    Router::get('/reviews/{id}', 'AdminController@viewReview');
    Router::post('/reviews/{id}/approve', 'AdminController@approveReview');
    Router::post('/reviews/{id}/reject', 'AdminController@rejectReview');
    Router::post('/reviews/{id}/delete', 'AdminController@deleteReview');
    Router::post('/reviews/bulk-approve', 'AdminController@bulkApproveReviews');
    Router::post('/pandits/{id}/verify-documents', 'AdminController@verifyPanditDocuments');
});

// ============================================================
// PANDIT ROUTES
// ============================================================
Router::group(['prefix' => 'pandit', 'middleware' => ['pandit']], function () {
    // Dashboard
    Router::get('/dashboard', 'PanditController@dashboard', [], 'pandit.dashboard');

    // Profile
    Router::get('/profile', 'PanditController@profile', [], 'pandit.profile');
    Router::post('/profile', 'PanditController@updateProfile');
    Router::post('/profile/password', 'PanditController@updatePassword');

    // Assignments
    Router::get('/assignments', 'PanditController@assignments', [], 'pandit.assignments');
    Router::get('/booking-requests', 'PanditController@bookingRequests', [], 'pandit.booking-requests');
    Router::post('/assignments/{id}/confirm', 'PanditController@confirmAssignment');
    Router::post('/assignments/{id}/complete', 'PanditController@completeAssignment');

    // Questions
    Router::get('/questions', 'PanditController@questions', [], 'pandit.questions');
    Router::post('/questions/{id}/answer', 'PanditController@answerQuestion');

    // Custom Rituals Validation
    Router::get('/custom-rituals', 'PanditController@customRituals');
    Router::post('/custom-rituals/{id}/validate', 'PanditController@validateCustomRitual');

    // Client Ritual Management
    Router::get('/assignments/{id}/ritual', 'PanditController@viewAssignmentRitual');
    Router::post('/assignments/{id}/ritual/steps', 'PanditController@addClientRitualStep');
    Router::post('/client-ritual-steps/{id}/update', 'PanditController@updateClientRitualStep');
    Router::post('/client-ritual-steps/{id}/delete', 'PanditController@deleteClientRitualStep');
});

// ============================================================
// USER ROUTES
// ============================================================
Router::group(['prefix' => 'user', 'middleware' => ['user']], function () {
    // Dashboard
    Router::get('/dashboard', 'UserController@dashboard', [], 'user.dashboard');

    // Profile
    Router::get('/profile', 'UserController@profile', [], 'user.profile');
    Router::post('/profile', 'UserController@updateProfile');
    Router::post('/profile/password', 'UserController@updatePassword');

    // Family Management
    Router::get('/families', 'UserController@families', [], 'user.families');
    Router::get('/families/create', 'UserController@createFamily');
    Router::post('/families', 'UserController@storeFamily');
    Router::get('/families/{id}/edit', 'UserController@editFamily');
    Router::post('/families/{id}', 'UserController@updateFamily');
    Router::post('/families/{id}/members', 'UserController@addFamilyMember');
    Router::post('/families/members/{id}/delete', 'UserController@deleteFamilyMember');
    Router::post('/families/{id}/delete', 'UserController@deleteFamily');

    // Explore Rituals (Enhanced with AI)
    Router::get('/rituals', 'UserController@rituals', [], 'user.rituals');
    Router::get('/rituals/search', 'UserController@searchRituals');
    Router::post('/rituals/generate', 'UserController@generateRitual');
    Router::post('/rituals/load-more', 'UserController@loadMoreRituals');
    Router::get('/rituals/{id}', 'UserController@viewRitual');

    // My Rituals (Personal Collection)
    Router::get('/my-rituals', 'UserController@myRituals', [], 'user.my-rituals');
    Router::post('/my-rituals/add', 'UserController@addToMyRituals');
    Router::post('/my-rituals/add-generated', 'UserController@addGeneratedToMyRituals');
    Router::get('/my-rituals/{id}', 'UserController@viewMyRitual');
    Router::get('/my-rituals/{id}/pdf', 'UserController@downloadRitualPdf');
    Router::get('/my-rituals/{id}/start', 'UserController@startRitual');
    Router::post('/my-rituals/{id}/steps', 'UserController@addMyRitualStep');
    Router::post('/my-rituals/steps/{id}', 'UserController@updateMyRitualStep');
    Router::post('/my-rituals/steps/{id}/delete', 'UserController@deleteMyRitualStep');
    Router::post('/my-rituals/{id}/items', 'UserController@addMyRitualItem');
    Router::post('/my-rituals/items/{id}', 'UserController@updateMyRitualItem');
    Router::post('/my-rituals/items/{id}/delete', 'UserController@deleteMyRitualItem');
    Router::post('/my-rituals/{id}/delete', 'UserController@deleteMyRitual');

    // Ritual Execution & Progress
    Router::post('/ritual/complete-step', 'UserController@completeStep');
    Router::post('/ritual/complete', 'UserController@completeRitual');
    Router::post('/ritual/chat', 'UserController@ritualChat');

    // Custom Rituals (Legacy)
    Router::get('/custom-rituals', 'UserController@customRituals');
    Router::get('/custom-rituals/create', 'UserController@createCustomRitual');
    Router::post('/custom-rituals', 'UserController@storeCustomRitual');
    Router::get('/custom-rituals/{id}', 'UserController@viewCustomRitual');
    Router::post('/custom-rituals/{id}/submit', 'UserController@submitCustomRitual');
    Router::post('/custom-rituals/{id}/delete', 'UserController@deleteCustomRitual');

    // Pandit Selection & Booking
    Router::get('/select-pandit', 'UserController@selectPandit');
    Router::get('/book-pandit/{panditId}', 'UserController@showBookingForm');
    Router::post('/book-pandit', 'UserController@bookPandit');
    Router::get('/bookings', 'UserController@bookings', [], 'user.bookings');
    Router::post('/bookings/{id}/cancel', 'UserController@cancelBooking');

    // Shopping List
    Router::get('/shopping-list', 'UserController@shoppingList', [], 'user.shopping');
    Router::post('/shopping-list', 'UserController@addToShoppingList');
    Router::get('/shopping-list/generate/{id}', 'UserController@generateShoppingList');
    Router::post('/shopping-list/find-shops', 'UserController@findShops');
    Router::post('/shopping-list/{id}/purchased', 'UserController@markItemPurchased');
    Router::post('/shopping-list/{id}/unpurchased', 'UserController@markItemUnpurchased');
    Router::post('/shopping-list/{id}/update-quantity', 'UserController@updateItemQuantity');
    
    // Checkout Flow
    Router::get('/shopping-list/checkout', 'UserController@shoppingCheckout');
    Router::post('/shopping-list/find-nearby-shops', 'UserController@findNearbyShopsForCart');
    Router::post('/shopping-list/place-order', 'UserController@placeOrder');
    
    // Orders
    Router::get('/orders', 'UserController@orders', [], 'user.orders');
    Router::get('/orders/{id}', 'UserController@viewOrder');

    // AI Suggestions
    Router::get('/ai-suggestions', 'UserController@aiSuggestions', [], 'user.ai');
    Router::post('/ai-suggestions', 'UserController@getAISuggestion');

    // Cultural Insights
    Router::get('/insights', 'UserController@insights', [], 'user.insights');
    Router::get('/insights/{slug}', 'UserController@viewInsight');

    // Ask Pandit / Q&A
    Router::get('/questions', 'UserController@questions', [], 'user.questions');
    Router::post('/ask-pandit', 'UserController@askPandit');

    // Vendor Browsing
    Router::get('/vendors', 'UserController@vendors', [], 'user.vendors');
    Router::post('/vendors/nearby', 'UserController@findNearbyVendors');
    Router::get('/vendors/{id}', 'UserController@viewVendor');

    // Review System
    Router::get('/reviews/pandit/{assignmentId}', 'UserController@reviewPanditForm');
    Router::post('/reviews/pandit', 'UserController@submitPanditReview');
    Router::get('/reviews/vendor/{orderId}', 'UserController@reviewVendorForm');
    Router::post('/reviews/vendor', 'UserController@submitVendorReview');
    Router::get('/my-reviews', 'UserController@myReviews', [], 'user.my-reviews');
    Router::get('/review-notifications', 'UserController@reviewNotifications');
});

