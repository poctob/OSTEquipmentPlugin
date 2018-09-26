import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { StatusComponent } from './status/status.component';
import { EqpdashboardComponent } from './eqpdashboard/eqpdashboard.component';

const routes: Routes = [
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
  { path: 'dashboard', component: EqpdashboardComponent },
  { path: 'status', component: StatusComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
