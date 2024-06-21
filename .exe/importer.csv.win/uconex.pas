unit uConex;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, DB, ZConnection, ZDataset;

type

  { TDM }

  TDM = class(TDataModule)
    qry_config: TZQuery;
    qry_configid: TLargeintField;
    qry_confignumero_whatsapp: TMemoField;
    qry_configsenha: TMemoField;
    qry_configtoken: TMemoField;
    qry_produtosidentificador: TMemoField;
    qry_produtospath_img: TMemoField;
    qry_produtosstatus: TMemoField;
    ZConnection1: TZConnection;
    qry_produtos: TZQuery;
    qry_produtoscodigo_barras: TMemoField;
    qry_produtosdescricao: TMemoField;
    qry_produtosid: TLargeintField;
    qry_produtosnome: TMemoField;
    qry_produtospreco: TFloatField;
    qry_produtosquantidade: TFloatField;
    ZTable1codigo_barras: TMemoField;
    ZTable1descricao: TMemoField;
    ZTable1id: TLargeintField;
    ZTable1identificador: TMemoField;
    ZTable1nome: TMemoField;
    ZTable1path_img: TMemoField;
    ZTable1preco: TFloatField;
    ZTable1quantidade: TFloatField;
  private

  public

  end;

var
  DM: TDM;

implementation

{$R *.lfm}

end.

