<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartiglioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cartiglio', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('Cod_Bar')->nullable();		
			$table->string('Cod_Art_CZ')->nullable();
			$table->string('Cod_Col_CZ')->nullable();
			$table->string('Tgl_ITA')->nullable();
			$table->string('Tgl_ENG')->nullable();
			$table->string('Tgl_SPA')->nullable();
			$table->string('Tgl_EUR')->nullable();
			$table->string('Tgl_USA')->nullable();
			$table->string('Descr_Col_CZ')->nullable();

			// $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cartiglio');
	}

}


/*
USE [finalaudit]
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[cartiglio](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[Cod_Bar] [nvarchar](255) NOT NULL,
	[Cod_Art_CZ] [nvarchar](255) NOT NULL,
	[Cod_Col_CZ] [nvarchar](255) NOT NULL,
	[Tgl_EUR] [nvarchar](255) NOT NULL,
	[Descr_Col_CZ] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

*/
/*
USE [finalaudit]
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[cartiglio](
	[id] [int] IDENTITY(1,1)  NULL,
	[Cod_Bar] [nvarchar](255)  NULL,
	[Cod_Art_CZ] [nvarchar](255)  NULL,
	[Cod_Col_CZ] [nvarchar](255)  NULL,
	[Tgl_ITA] [nvarchar](255) NULL,
	[Tgl_ENG] [nvarchar](255)  NULL,
	[Tgl_SPA] [nvarchar](255)  NULL,
	[Tgl_EUR] [nvarchar](255)  NULL,
	[Tgl_USA] [nvarchar](255)  NULL,
	[Descr_Col_CZ] [nvarchar](255)  NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
*/