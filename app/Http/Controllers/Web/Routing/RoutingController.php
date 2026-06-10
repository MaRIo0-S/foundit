<?php

namespace App\Http\Controllers\Web\Routing;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Services\CategoriesService;
use App\Services\HomeService;
use App\Services\ItemsService;
use App\Services\LocationsService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoutingController extends Controller
{
    private ItemsService $itemsService;
    private CategoriesService $categoriesService;
    private LocationsService $locationsService;
    private HomeService $homeService;
    private UserService $userService;
    public function __construct(
        ItemsService $itemsService, 
        CategoriesService $categoriesService, 
        LocationsService $locationsService,
        HomeService $homeService,
        UserService $userService
    )
    {
        $this->userService = $userService;
        $this->homeService = $homeService;
        $this->itemsService = $itemsService;
        $this->categoriesService = $categoriesService;
        $this->locationsService = $locationsService;
    }
    
    public function home()
    {
        $users = $this->homeService->users();
        $items = $this->homeService->items();
        $claims = $this->homeService->claims();
        return Inertia::render('Home', [
            'users' => $users,
            'items' => $items,
            'claims' => $claims
        ]);
    }

    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    public function register()
    {
        return Inertia::render('Auth/Register');
    }

    public function dashboard()
    {
        $items = $this->itemsService->Items();
        $categories = $this->categoriesService->categories();
        $locations = $this->locationsService->locations();
        return Inertia::render('Dashboard', [
            'items' => $items,
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function category($id)
    {
        $category = $this->categoriesService->category($id);
        return Inertia::render('Items/Category', [
            'category' => $category
        ]);
    }
    public function location($id)
    {
        $location = $this->locationsService->location($id);
        return Inertia::render('Items/Location', [
            'location' => $location
        ]);
    }

    public function declaration(){
        $categories = $this->categoriesService->categories();
        $locations = $this->locationsService->locations();
        return Inertia::render('Items/Declare', [
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function profileEdit(){
        return Inertia::render('User/ModifyProfil');
    }

    public function profileReclamations(){
        $reclamations = $this->userService->reclamations();
        return Inertia::render('User/Reclamations', [
            'reclamations' => $reclamations
        ]);
    }
    public function profileDeclarations(){
        $declarations = $this->userService->declarations();
        return Inertia::render('User/Declarations', [
            'declarations' => $declarations
        ]);
    }
    public function claimModify(Claim $claim){
        return Inertia::render('User/ModifyReclamation',[
            'reclamation' => $claim
        ]);
    }
    public function declarationModify(Item $item){
        $categories = $this->categoriesService->categories();
        $locations = $this->locationsService->locations();
        return Inertia::render('User/ModifyDeclaration',[
            'declaration' => $item,
            'categories' => $categories,
            'locations' => $locations
        ]);
    }
}