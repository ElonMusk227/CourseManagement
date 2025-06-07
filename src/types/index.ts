export interface User {
  id: number;
  nom: string;
  prenom: string;
  tel: string;
  mail: string;
  role: 'student' | 'teacher' | 'admin';
  password: string;
}

export interface Teacher extends User {
  role: 'teacher';
  datePriseFonction: string;
  departement: string;
  indice: number;
}

export interface Student extends User {
  role: 'student';
  anneeEntree: number;
}

export interface Subject {
  id: number;
  nom: string;
  code: string;
  credits: number;
  departement: string;
}

export interface Grade {
  id: number;
  studentId: number;
  subjectId: number;
  teacherId: number;
  note: number;
  dateEvaluation: string;
}

export interface TeacherSubject {
  teacherId: number;
  subjectId: number;
}

export interface StudentSubject {
  studentId: number;
  subjectId: number;
}