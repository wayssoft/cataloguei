program Cataloguei;

{$mode objfpc}{$H+}

uses
  {$IFDEF UNIX}
  cthreads,
  {$ENDIF}
  {$IFDEF HASAMIGA}
  athreads,
  {$ENDIF}
  Interfaces, // this includes the LCL widgetset
  Forms, zcomponent, uPrincipal, uLogin, uRequest4Pascal, uImportarCSV, uConex,
  uFinish, uFiltro
  { you can add units after this };

{$R *.res}

begin
  RequireDerivedFormResource:=True;
  Application.Scaled:=True;
  Application.Initialize;
  Application.CreateForm(TDM, DM);
  Application.CreateForm(TfrmPrincipal, frmPrincipal);
  Application.CreateForm(TFrmImportarCsvProdutos, FrmImportarCsvProdutos);
  Application.CreateForm(TfrmFinish, frmFinish);
  Application.CreateForm(TfrmFiltro, frmFiltro);
  Application.Run;
end.

