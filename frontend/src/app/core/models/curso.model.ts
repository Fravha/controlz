export type NivelCurso = 'basico' | 'intermedio' | 'avanzado';

export interface Curso {
  id: number;
  titulo: string;
  descripcion: string;
  duracionHoras: number;
  nivel: NivelCurso;
  categoria: string;
  precioBase: number;
  planRequerido: 'basico' | 'pro' | 'premium' | null;
  instructorId: number;
  instructorNombre?: string;
  temario?: string[];
  requisitos?: string[];
  portadaUrl?: string;
  inscritos?: number;
}

export interface CursoFiltro {
  nivel?: NivelCurso;
  categoria?: string;
  precioMax?: number;
  q?: string;
}
