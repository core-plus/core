<?php

namespace Core\API\Controllers\User;

use Illuminate\Http\Request;
use Core\API\Controllers\Controller;
use Core\API\Resources\Ability as AbilityResource;

class AbilityController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * List all abilities.
     */
    public function __invoke(Request $request)
    {
        return AbilityResource::collection(
            $request->user()->ability()->all()->values()
        );
    }
}
