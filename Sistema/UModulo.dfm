object DataModuleBookBox: TDataModuleBookBox
  Height = 480
  Width = 640
  object ConnectionBookBox: TFDConnection
    Params.Strings = (
      'Database=dbbookbox'
      'User_Name=root'
      'Server=localhost'
      'DriverID=MySQL')
    Connected = True
    LoginPrompt = False
    Left = 272
    Top = 40
  end
  object ConsultaUsuarios: TFDQuery
    Active = True
    Connection = ConnectionBookBox
    SQL.Strings = (
      'SELECT * FROM tbusuarios')
    Left = 272
    Top = 128
  end
end
