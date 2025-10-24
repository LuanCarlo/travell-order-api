@component('mail::message')
# Olá, {{ $order->user->name }}!

O status do seu pedido de viagem para **{{ $order->destination }}** (ID: #{{ $order->id }}) foi atualizado com sucesso.

O novo status é: **{{ $newStatusLabel }}**.

Você pode verificar todos os detalhes do seu pedido clicando no botão abaixo.

@component('mail::button', ['url' => url('/orders/' . $order->id)])
Ver Detalhes do Pedido
@endcomponent

Obrigado por utilizar nossa plataforma.

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent