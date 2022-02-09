<x-app-layout>
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ご購入ありがとうございました。
        </h2>
    </x-slot>


    <div class="py-12">
    
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class=" bg-white md:flex md:items-center mb-4  text-xl text-gray-800 leading-tight">
            購入履歴
             </h2>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            
              <div class="p-6 bg-white border-b border-gray-200">
                  @if (count($historyInfo) > 0)
                    @foreach ($historyInfo as $history )
                   
                      <div class="md:flex md:items-center mb-2">
                        <div class="md:w-3/12">
                          @if ($history->product->imageFirst->filename !== null)
                          <img src="{{ asset('storage/products/' . $history->product->imageFirst->filename )}}">
                          @else
                          <img src="">
                          @endif
                        </div>
                        <div class="md:w-4/12 md:ml-2">{{ $history->product->name }}</div>
                        <div class="md:w-3/12 flex justify-around">
                          <div>{{ $history->quantity}}個</div>
                          <div>{{ number_format($history->product->price )}}<span class="text-sm text-gray-700">円(税込)</span></div>
                        </div>  
                      </div>
                      
                    @endforeach
                  @else
                    カートに商品が入っていません。
                  @endif
              </div>
          </div>
      </div>
  </div>
    

</x-app-layout>
