export type EstadoPago = 'pendiente' | 'confirmado' | 'rechazado';

export interface Pago {
  id: number;
  usuarioId: number;
  suscripcionId: number;
  monto: number;
  fechaPago: string;
  transaccionId: string;
  estado: EstadoPago;
  metodo?: 'stripe' | 'paypal_sandbox';
}

export interface IniciarPagoRequest {
  planId: number;
}

export interface IniciarPagoResponse {
  checkoutUrl: string;
  transaccionId: string;
}
