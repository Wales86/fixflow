<?php

namespace App\Services;

use App\Dto\Client\ClientData;
use App\Dto\Client\ClientListItemData;
use App\Dto\Client\ClientSelectOptionData;
use App\Dto\Client\ClientShowPagePropsData;
use App\Dto\Client\StoreClientData;
use App\Dto\Client\UpdateClientData;
use App\Dto\Vehicle\VehicleData;
use App\Exceptions\CannotDeleteClientWithVehiclesException;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;

class ClientService
{
    public function paginatedList(array $filters = []): LengthAwarePaginator
    {
        $query = Client::query()->withCount('vehicles');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['sort'])) {
            $direction = $filters['direction'] ?? 'asc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->latest('created_at');
        }

        return $query->paginate(15)
            ->through(fn ($client) => ClientListItemData::from($client));
    }

    public function create(StoreClientData $data): Client
    {
        return Client::create($data->all());
    }

    public function update(Client $client, UpdateClientData $data): Client
    {
        $client->update($data->all());

        return $client->fresh();
    }

    public function prepareClientShowData(Client $client): ClientShowPagePropsData
    {
        $client->load('vehicles');
        $client->vehicles->loadCount('repairOrders');

        return ClientShowPagePropsData::from([
            'client' => ClientData::from($client),
            'vehicles' => VehicleData::collect($client->vehicles),
        ]);
    }

    /**
     * @throws CannotDeleteClientWithVehiclesException
     */
    public function deleteClient(Client $client): void
    {
        if ($client->vehicles()->exists()) {
            throw new CannotDeleteClientWithVehiclesException(
                'Cannot delete client with associated vehicles.'
            );
        }

        try {
            $client->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting client: '.$e->getMessage());

            throw $e;
        }
    }

    public function getClientsForSelect(): DataCollection
    {
        $clients = Client::query()
            ->select(['id', 'first_name', 'last_name'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return ClientSelectOptionData::collect(items: $clients, into: DataCollection::class);
    }
}
