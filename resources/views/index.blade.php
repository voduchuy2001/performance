<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    @include('css')
</head>

<body>
    <div class="container py-12 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <button id="toggle" class="focus:outline-none 
            focus:ring-2 
            focus:ring-offset-2 
            focus:ring-indigo-700 
            mx-auto 
            transition
             duration-150
              ease-in-out hover:bg-indigo-600 bg-indigo-700 rounded text-white
             px-4 sm:px-8 py-2 text-xs sm:text-sm
             text-center inline-flex items-center mr-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-4 h-4 mr-2 -ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
        
                Import Data from CSV
            </button>
        </div>
        
        <div class="relative overflow-x-auto rounded-sm">
            <table id="product-table" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Id
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            SKU
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Stock
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Satus
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Vendor
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Created At
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($products->isNotEmpty())
                    @foreach ($products as $product)
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->name }}">
                            {{ $product->id }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->name }}">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->sku }}">
                            {{ $product->sku }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->description }}">
                            {{ $product->description }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->price }}">
                            {{ $product->price }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->status }}">
                            {{ $product->status }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->stock }}">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->type }}">
                            {{ $product->type }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->vendor }}">
                            {{ $product->vendor }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $product->name }}">
                            {{ $product->created_at }}
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr class="bg-white border-b">
                        <td colspan="100%">
                            <div class="text-center py-6">
                                @include('components.icons.empty-state-icon')

                                <p class="mt-3 text-base font-semibold text-gray-900 text-uppercase">There's no data
                                    available
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div id="pagination" class="mt-6">
            {{ $products->links('components.pagination') }}
        </div>
    </div>

    @include('components.modal')

    @include('js')

</body>

</html>