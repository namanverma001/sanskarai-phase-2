<?php
/**
 * Sanskar AI - Home Controller
 * =============================
 * Handles public pages like landing page
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Ritual;
use App\Models\User;
use App\Models\CulturalInsight;

class HomeController extends Controller
{
    /**
     * Landing page
     */
    public function index(): void
    {
        // Don't redirect if logged in - let users see landing page with their profile in navbar
        
        // Get some stats for the landing page
        $ritualModel = new Ritual();
        $userModel = new User();
        $insightModel = new CulturalInsight();
        
        $data = [
            'title' => 'Sanskar AI - Your Guide to Hindu Rituals & Traditions',
            'featuredRituals' => $ritualModel->getFeatured(3),
            'stats' => [
                'rituals' => $ritualModel->count(['is_active' => 1]),
                'pandits' => count($userModel->getApprovedPandits()),
                'insights' => $insightModel->count(['is_published' => 1]),
            ],
        ];
        
        $this->viewWithLayout('home/landing', 'layouts/landing', $data);
    }
    
    /**
     * About page
     */
    public function about(): void
    {
        $this->viewWithLayout('home/about', 'layouts/landing', [
            'title' => 'About - Sanskar AI'
        ]);
    }
    
    /**
     * Contact page
     */
    public function contact(): void
    {
        $this->viewWithLayout('home/contact', 'layouts/landing', [
            'title' => 'Contact - Sanskar AI'
        ]);
    }
}
