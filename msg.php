<?php
string connectionString = "Server="localhost";Database="studio";Integrated Security=True;";

using System;
using System.Data.SqlClient;
using System.Windows.Forms;

public class MessageRetriever
{
    public void DisplayMessages()
    {
        string connectionString = "Server=your_server_name;Database=your_database_name;Integrated Security=True;";
        string query = "SELECT MessageContent FROM Messages"; // Replace with your actual table and column names

        try
        {
            using (SqlConnection connection = new SqlConnection(connectionString))
            {
                connection.Open();
                using (SqlCommand command = new SqlCommand(query, connection))
                {
                    using (SqlDataReader reader = command.ExecuteReader())
                    {
                        string messages = string.Empty;
                        while (reader.Read())
                        {
                            messages += reader["MessageContent"].ToString() + Environment.NewLine;
                        }

                        if (!string.IsNullOrEmpty(messages))
                        {
                            MessageBox.Show(messages, "Messages from Database", MessageBoxButtons.OK, MessageBoxIcon.Information);
                        }
                        else
                        {
                            MessageBox.Show("No messages found.", "Information", MessageBoxButtons.OK, MessageBoxIcon.Information);
                        }
                    }
                }
            }
        }
        catch (Exception ex)
        {
            MessageBox.Show($"An error occurred: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
        }
    }
}
MessageRetriever retriever = new MessageRetriever();
retriever.DisplayMessages();
?>
