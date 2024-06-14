<?php

namespace App\Http\Controllers;

use App\IdentityProviders\FirstProvider;
use App\IdentityProviders\SecondProvider;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use App\TrackTikApi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\NoReturn;

class EmployeesController extends Controller
{

    private TrackTikApi $client;
    private array $pathToIdentityProvider;

    public function __construct(TrackTikApi $client)
    {
        $this->client = $client;
        $this->pathToIdentityProvider = [
            'first' => FirstProvider::class,
            'second' => SecondProvider::class
        ];
    }
    public function index(): JsonResponse
    {
        $res = $this->client->getEmployees();
        return response()->json($res['data']);
    }
    public function getRegions(): JsonResponse
    {
        $res = $this->client->getRegions();
        return response()->json($res['data']);
    }

    public function create(Request $request, string $idp): JsonResponse
    {
        if (!array_key_exists($idp, $this->pathToIdentityProvider)) {
            return response()->json(['error' => 'Incorrect route'], 500);
        }
        $provider = $this->pathToIdentityProvider[$idp];
        $entity = new $provider;
        $rules = $entity->getValidationRules();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 500);
        }

        $data = $entity->mapper((object)$request->all());
        $res = $this->client->createEmployee($data);
        return response()->json($res['data']);
    }

    public function update(Request $request, string $idp, string $id): JsonResponse
    {
        if (!array_key_exists($idp, $this->pathToIdentityProvider) || empty($id)) {
            return response()->json(['error' => 'Incorrect route'], 500);
        }
        $provider = $this->pathToIdentityProvider[$idp];
        $entity = new $provider;
        $rules = $entity->getValidationRules(true);

        $validator = Validator::make($request->all(), $rules);

        if (empty($request->all()) || $validator->fails()) {
            return response()->json(['error' => 'Nothing to update'], 500);
        }
        $data = $entity->mapper((object)$request->all());
        try {
            $res = $this->client->updateEmployee($id, $data);
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json($res['data']);
    }
}
