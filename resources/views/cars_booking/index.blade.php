@extends('layout')

@section('content')
    <div class="flex">
       <div>
        <div class="m-4 p-4">
            <form class="flex-col">
               <div class="text-sm text-slate-900 mb-1 flex space-x-10">
                <label for="start_date"
                    class="font-bold"
                > Start date
                    <input type="date" name='start_date' value="{{ $start_date }}"
                    class="p-1 border-2 rounded-md border-slate-300">
                </label>
                <label for="end_date"
                    class="font-bold"
                > End date
                    <input type="date" name='end_date' value="{{ $end_date }}"
                    class="p-1 border-2 rounded-md border-slate-300">
                </label>
               </div>
               <div class="text-sm text-slate-900 mb-1 flex space-x-10">
                <label for="company" class="font-bold">
                    Company
                    <select name="company"
                    class="p-1 border-2 rounded-md border-slate-300 bg-slate-50">
                    @foreach ($companies as $c )
                         <option value="{{$c->company_id}}" {{ $select_company == $c->company_id ? 'selected' : ''}}>
                             @if(isset($c->company_id))
                                 Company {{$c->company_id}}
                             @else
                                 Unknow
                             @endif
                         </option>
                    @endforeach
                 </select>
                </label>
                 <label for="lang" class="font-bold">
                    Lang
                    <select name="lang"
                    class="p-1 border-2 rounded-md border-slate-300 bg-slate-50">
                     @foreach ($langs as $l )
                          <option value="{{$l->lang}}" {{ $select_lang == $l->lang ? 'selected' : ''}}>
                              @if(isset($l->lang))
                                 {{$l->lang}}
                              @else
                                  Unknow
                              @endif
                          </option>
                     @endforeach
                  </select>
                 </label>
                  <button class="p-1  rounded-md border-2 border-slate-500 font-bold bg-slate-200 hover:bg-slate-100 ">Show</button>
                </div>
           </form>
           </div>
           
            <div class="ml-4">
                <div>
                    <p>All cars: <span>{{ count($cars) }}</span></p>
                   </div>
            
                <table class="table">
                    <tr class="bg-slate-100">
                        <th class="ceil">id</th>
                        <th class="ceil">Name</th>
                        <th class="ceil">Year</th>
                        <th class="ceil">Color</th>
                        <th class="ceil">Number</th>
                        <th class="ceil">Free</th>
                        <th class="ceil">Service</th>
                        <th class="ceil">Busy</th>
                        <th class="ceil">All</th>
                    </tr>
                    @foreach ($cars as $car_id => $car)
                        <tr class="row">
                            <td class="ceil">{{ $car_id }} </td>
                            <td class="ceil">
                                <a href="" class="car-link" car_id={{ $car_id }}>{{ $car['name'] }} </a>
                            </td>
                            <td class="ceil">{{ $car['year'] }} </td>
                            <td class="ceil">{{ $car['color'] }} </td>
                            <td class="ceil">{{ $car['number'] }} </td>
                            <td class="ceil">{{ $car['free'] }} </td>
                            <td class="ceil">{{ $car['service'] }} </td>
                            <td class="ceil">{{ $car['busy'] }} </td>
                            <td class="ceil">{{ $car['all'] }} </td>
                        </tr>
                    @endforeach
                </table> 
            </div>
       </div>
        <div class="m-2 p-2 right-1 w-2/5 fixed">
          <h2 class="text-lg font-bold">  Time When Car is Busy</h2>
            @foreach ($bookings as $b )
                <div class="booking" hidden car_id="{{ $b->car_id }}">
                    <div class="m-2 ">
                        <span>{{ $b->start_date }}</span> -
                        <span>{{ $b->end_date }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection