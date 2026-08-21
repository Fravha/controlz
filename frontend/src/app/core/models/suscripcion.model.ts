export type NombrePlan = 'Basico' | 'Pro' | 'Premium';

export interface PlanSuscripcion {
  id: number;
  nombre: NombrePlan;
  precio: number;
  duracionDias: number;
  maxCursos: number | null; // null = ilimitado (Premium)
  descripcion?: string;
}

export type EstadoSuscripcion = 'activa' | 'vencida' | 'cancelada' | 'pendiente_pago';

export interface Suscripcion {
  id: number;
  usuarioId: number;
  planId: number;
  plan?: PlanSuscripcion;
  fechaInicio: string;
  fechaFin: string;
  estado: EstadoSuscripcion;
  renovacionAutomatica: boolean;
}
