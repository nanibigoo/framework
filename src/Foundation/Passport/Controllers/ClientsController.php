<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-10-28 14:00
 */
namespace Notadd\Foundation\Passport\Controllers;

use Illuminate\Http\Response;
use Illuminate\Validation\Factory as ValidationFactory;
use Laravel\Passport\ClientRepository;
use Notadd\Foundation\Routing\Abstracts\Controller;

/**
 * Class ClientsController.
 */
class ClientsController extends Controller
{
    /**
     * @var \Laravel\Passport\ClientRepository
     */
    protected $clients;
    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validation;

    /**
     * ClientsController constructor.
     *
     * @param \Laravel\Passport\ClientRepository $clients
     * @param \Illuminate\Validation\Factory     $validation
     */
    public function __construct(ClientRepository $clients, ValidationFactory $validation)
    {
        parent::__construct();
        $this->clients = $clients;
        $this->validation = $validation;
    }

    /**
     * @param $clientId
     *
     * @return \Illuminate\Http\Response|null
     */
    public function destroy($clientId)
    {
        if (!$this->request->user()->clients->find($clientId)) {
            return new Response('', 404);
        }
        $this->clients->delete($this->request->user()->clients->find($clientId));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $userId = $this->request->user()->getKey();

        return $this->clients->activeForUser($userId)->makeVisible('secret');
    }

    /**
     * @return \Laravel\Passport\Client
     */
    public function store()
    {
        $this->validation->make($this->request->all(), [
            'name'     => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();

        return $this->clients->create($this->request->user()->getKey(), $this->request->name, $request->redirect)->makeVisible('secret');
    }

    /**
     * @param $clientId
     *
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     */
    public function update($clientId)
    {
        if (!$request->user()->clients->find($clientId)) {
            return new Response('', 404);
        }
        $this->validation->make($request->all(), [
            'name'     => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();

        return $this->clients->update($request->user()->clients->find($clientId), $request->name, $request->redirect);
    }
}