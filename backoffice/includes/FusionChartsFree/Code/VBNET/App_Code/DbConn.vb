Imports Microsoft.VisualBasic
Imports System.Data.Odbc
Imports System.Data
Imports System.Web

Namespace DataConnection
    Public Class DbConn
        Public connection As OdbcConnection
        Public ReadData As OdbcDataReader
        Public aCommand As OdbcCommand
        Public Sub New(ByVal strQuery As String)
            Dim ConnectionString As String
            ' Setup Connection string For Access DataBase
            ConnectionString = "Driver={Microsoft Access Driver (*.mdb)};Dbq=" & HttpContext.Current.Server.MapPath("..\App_Data\FactoryDB.mdb")

            ' Setup Connection string For SQL Server DataBase
            'ConnectionString = "Driver={SQL Server};;uid=sa;pwd=;server=Infosoft;database=FactoryDB"

            ' Creating OdbcConnection Oject
            connection = New OdbcConnection()

            ' Setting Conection String
            connection.ConnectionString = ConnectionString
            ' Open Connection
            connection.Open()
            GetReader(strQuery)

        End Sub
        ' Create an instance dataReader
        ' Return type object of OdbcDataReader
        Public Sub GetReader(ByVal strQuery As String)

            '  Create a Command object
            aCommand = New OdbcCommand(strQuery, connection)

            ' Create data reader object using strQuery string
            ReadData = aCommand.ExecuteReader(CommandBehavior.CloseConnection)

        End Sub
    End Class
End Namespace

