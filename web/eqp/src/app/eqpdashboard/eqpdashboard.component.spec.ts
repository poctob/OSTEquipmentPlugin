
import { fakeAsync, ComponentFixture, TestBed } from '@angular/core/testing';

import { EqpdashboardComponent } from './eqpdashboard.component';

describe('EqpdashboardComponent', () => {
  let component: EqpdashboardComponent;
  let fixture: ComponentFixture<EqpdashboardComponent>;

  beforeEach(fakeAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ EqpdashboardComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EqpdashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should compile', () => {
    expect(component).toBeTruthy();
  });
});
