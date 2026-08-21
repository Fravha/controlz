export interface IngresoMensual {
  mes: string; // 'YYYY-MM'
  plan: string;
  totalIngresos: number;
}

export interface CursoPopular {
  cursoId: number;
  titulo: string;
  totalInscritos: number;
}

export interface EstudiantesActivosInactivos {
  activos: number;
  inactivos: number;
}
