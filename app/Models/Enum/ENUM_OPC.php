<?php

namespace App\Models\Enum;

//Lista de tipos de auditoría disponible en el sistema
class ENUM_OPC {
		public const OPCION_SIN_SESION = -12345;
		public const OPCION_GENERAL = 0;
		public const CONFIGURACION = 1;
		public const SEGURIDAD = 2;
		public const ATENCION_AL_PACIENTE = 3;
		public const MIS_TAREAS = 4;
		public const CONSULTAR_CASO = 30;
		public const MIS_PACIENTES = 31;
		public const SOLICITAR_AYUDA = 32;
		public const TABLERO_CONTROL = 33;
		public const CASOS_INTERES = 34;
		public const CONSULTA_GEOGRAFICA = 35;
		public const REPORTE_GERENCIAL = 36;
		public const COMUNICACIONES = 37;
		public const CATALOGOS = 11;
		public const EAPB = 13;
		public const PARAMETRO = 14;
		public const UPGD_UIS = 15;
		public const AUDITORIA = 21;
		public const CAMBIAR_CLAVE = 22;
		public const USUARIOS = 25;
		public const PARTICIPANTES = 38;
		public const DEPARTAMENTO = 12;
		public const MI_INFORMACION_MEDICO = 23;
		public const MI_INFORMACION_RESPONSABLE = 24;
}