using System;
using System.Data;
using System.Data.Odbc;
using System.Web;

namespace DataConnection
{
	/// <summary>
	/// Summary description for DbHelper.
	/// </summary>
    public class DbConn
	{   
        
		//  Create a database Connection. using here Access Database
        //  Return type object of OdbcConnection

        public OdbcConnection connection;
        public OdbcDataReader ReadData;
        public OdbcCommand aCommand;
        
        public DbConn(string strQuery)
        {

            string ConnectionString = "Driver={Microsoft Access Driver (*.mdb)};Dbq=" + HttpContext.Current.Server.MapPath("..\\App_Data\\FactoryDB.mdb");

            //string ConnectionString = "Driver={SQL Server};;uid=sa;pwd=;server=Infosoft;database=FactoryDB";

            connection = new OdbcConnection();
            connection.ConnectionString = ConnectionString;
            connection.Open();
            GetReader(strQuery);
        }

     
        // Create an instance dataReader
        // Return type object of OdbcDataReader
        public void GetReader(string strQuery)
        {
            //  Create a Command object
            aCommand = new OdbcCommand(strQuery, connection);
            // Create data reader object using strQuery string

            ReadData = aCommand.ExecuteReader(CommandBehavior.CloseConnection);
            
        }
   	
	}
}